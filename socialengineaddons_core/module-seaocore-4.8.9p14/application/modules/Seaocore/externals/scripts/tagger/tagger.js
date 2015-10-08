var SEAOLasso = new Class({

  Implements : [Options, Events],

  active : false,

  options : {
    autoHide: true,
    cropMode : false,
    globalTrigger : false,
    min : false,
    max : false,
    ratio : false,
    contain : false,
    trigger : null,
    border : '#999',
    color : '#7389AE',
    opacity : .3,
    zindex : 10000,
    bgimage : './blank.gif'
  },
	
  binds : {},

  initialize : function(options){
    this.setOptions(options);
		
    this.box = new Element('div', { 
      'styles' : {
        'display' : 'none', 
        'position' : 'absolute',  
        'z-index' : this.options.zindex
      } 
    }).inject((this.container) ? this.container : document.body);
		
    this.overlay = new Element('div',{
      'class' : 'lasso-overlay',
      'styles' : {
        'position' : 'relative', /*
        'background' : 'url('+this.options.bgimage+')',*/ 'height' : '100%', 
        'width' : '100%',   
        'z-index' : this.options.zindex+1
      }
    }).inject(this.box);

    this.mask = new Element('div',{
      'styles' : {
        'position' : 'absolute', 
        'background-color' : this.options.color, 
        'opacity' : this.options.opacity, 
        'height' : '100%', 
        'width' : '100%', 
        'z-index' : this.options.zindex-1
      }
    });

    if(this.options.cropMode){
      this.mask.setStyle('z-index',this.options.zindex-2).inject(this.container);
      this.options.trigger = this.mask; // override trigger since we are a crop
    } else {
      this.mask.inject(this.overlay);
    }
		
    this.trigger = $(this.options.trigger);
		
    // Marching Ants
    var antStyles = {
      'position' : 'absolute', 
      'width' : 1, 
      'height' : 1, 
      'overflow' : 'hidden', 
      'z-index' : this.options.zindex+1
    };

    if( this.options.border.test(/\.(jpe?g|gif|png)/) ) antStyles.backgroundImage = 'url('+this.options.border+')';
    else var antBorder = '1px dashed '+this.options.border;

    this.marchingAnts = {};
    ['left','right','top','bottom'].each(function(side,idx){
      switch(side){
        case 'left' :
          style = $merge(antStyles,{
          top : 0, 
          left : -1, 
          height : '100%'
        });
        break;
        case 'right' :
          style = $merge(antStyles,{
          top : 0, 
          right : -1, 
          height : '100%'
        });
        break;
        case 'top' :
          style = $merge(antStyles,{
          top : -1, 
          left : 0, 
          width : '100%'
        });
        break;
        case 'bottom' :
          style = $merge(antStyles,{
          bottom : -1, 
          left : 0, 
          width : '100%'
        });
        break;
      }
      if(antBorder) style['border-'+side] = antBorder;
      this.marchingAnts[side] = new Element('div',{
        'styles' : style
      }).inject(this.overlay);
    },this);

    this.binds.start = this.start.bindWithEvent(this);
    this.binds.move = this.move.bindWithEvent(this);
    this.binds.end = this.end.bindWithEvent(this);

    this.attach();

    document.body.onselectstart = function(e){
      e = new Event(e).stop();
      return false;
    };

    // better alternative?
    this.removeDOMSelection = (document.selection && document.selection.empty) ? function(){
      document.selection.empty();
    } : 
    (window.getSelection) ? function(){
      var s=window.getSelection();
      if(s && s.removeAllRanges) s.removeAllRanges();
    } : $lambda(false);

    this.resetCoords();		
  },

  attach : function(){
    this.trigger.addEvent('mousedown', this.binds.start);
  },

  detach : function(){
    if(this.active) this.end();
    this.trigger.removeEvent('mousedown', this.binds.start);
  },

  start : function(event){
    if((!this.options.autoHide && event.target == this.box) || (!this.options.globalTrigger && (this.trigger != event.target))) return false;
    this.active = true;
    document.addEvents({
      'mousemove' : this.binds.move, 
      'mouseup' : this.binds.end
    });
    this.resetCoords();
    if(this.options.contain) this.getContainCoords();
    if(this.container) this.getRelativeOffset();
    this.setStartCoords(event.page);
    this.fireEvent('start');
    return true;
  },

  move : function(event){
    if(!this.active) return false;
		
    this.removeDOMSelection(); // clear as fast as possible!
		
    // saving bytes s = start, m = move, c = container
    var s = this.coords.start, m = event.page, box = this.coords.box = {}, c = this.coords.container;

    if(this.container){
      m.y -= this.offset.top;
      m.x -= this.offset.left;
    } 

    var f = this.flip = {
      y : (s.y > m.y), 
      x : (s.x > m.x)
    }; // flipping orgin? compare start to move
    box.y = (f.y) ? [m.y,s.y] : [s.y, m.y]; // order y
    box.x = (f.x) ? [m.x,s.x] : [s.x, m.x]; // order x

    if(this.options.contain){
      if(box.y[0] < c.y[0] ) box.y[0] = c.y[0]; // constrain top
      if(box.y[1] > c.y[1] ) box.y[1] = c.y[1]; // constrain bottom
      if(box.x[0] < c.x[0] ) box.x[0] = c.x[0]; // constrain left
      if(box.x[1] > c.x[1] ) box.x[1] = c.x[1]; // constrain right
    }
		
    if(this.options.max){ // max width & height
      if( box.x[1] - box.x[0] > this.options.max[0]){ // width is larger then max, fix
        if(f.x) box.x[0] = box.x[1] - this.options.max[0]; // if flipped
        else box.x[1] = box.x[0] + this.options.max[0]; // if normal
      }
      if( box.y[1] - box.y[0] > this.options.max[1]){ // height is larger then max, fix
        if(f.y) box.y[0] = box.y[1] - this.options.max[1]; // if flipped
        else box.y[1] = box.y[0] + this.options.max[1];  // if normal
      }
    }
	
    // ratio constraints
    if(this.options.ratio){ 
      var ratio = this.options.ratio;
      // get width/height divide by ratio
      var r = {
        x  : (box.x[1] - box.x[0]) / ratio[0],  
        y  : (box.y[1] - box.y[0]) / ratio[1]
      };
      if(r.x > r.y){ // if width ratio is bigger fix width
        if(f.x) box.x[0] =  box.x[1] - (r.y * ratio[0]); // if flipped width fix
        else  	box.x[1] =  box.x[0] + (r.y * ratio[0]); // normal width fix
      } else if( r.x < r.y){ // if height ratio is bigger fix height
        if(f.y) box.y[0] =  box.y[1] - (r.x * ratio[1]); // if flipped height fix
        else 	box.y[1] =  box.y[0] + (r.x * ratio[1]); // normal height fix
      }
    }

    this.refresh();
    return true;
  },
	
  refresh : function(){
    var c = this.coords, box = this.coords.box, cc = this.coords.container;
    c.w = box.x[1] - box.x[0];
    c.h = box.y[1] - box.y[0];
    c.top = box.y[0];
    c.left = box.x[0];
    this.box.setStyles({
      'display' : 'block',  
      'top' : c.top, 
      'left' : c.left, 
      'width' : c.w, 
      'height' : c.h
    });
    this.fireEvent('resize',this.getRelativeCoords());			
  },

  end : function(event){
    if(!this.active) return false;
    this.active = false;
    document.removeEvents({
      'mousemove' : this.binds.move, 
      'mouseup' : this.binds.end
    });
    if(this.options.autoHide) this.resetCoords();
    else if(this.options.min){
      if(this.coords.w < this.options.min[0] || this.coords.h < this.options.min[1]) this.resetCoords();
    }
    var ret = (this.options.autoHide) ? null : this.getRelativeCoords();
    this.fireEvent('complete',ret);
    return true;
  },

  setStartCoords : function(coords){
    if(this.container){
      coords.y -= this.offset.top;
      coords.x -= this.offset.left;
    } 
    this.coords.start = coords;
    this.coords.w = 0;
    this.coords.h = 0;
    this.box.setStyles({
      'display' : 'block', 
      'top' : this.coords.start.y, 
      'left' : this.coords.start.x
    });
  },

  resetCoords : function(){
    this.coords = {
      start : {
        x : 0, 
        y : 0
      }, 
      move : {
        x : 0, 
        y : 0
      }, 
      end : {
        x: 0, 
        y: 0
      }, 
      w: 0, 
      h: 0
    };
    this.box.setStyles({
      'display' : 'none', 
      'top' : 0, 
      'left' : 0, 
      'width' : 0, 
      'height' : 0
    });	
    this.getContainCoords();
  },
	
  getRelativeCoords : function(){
    var box = this.coords.box, cc = $merge(this.coords.container), c = this.coords;
    if(!this.options.contain) cc = {
      x : [0,0], 
      y : [0,0]
      };
    return {
      x : (box.x[0] - cc.x[0]).toInt(), 
      y : (box.y[0] - cc.y[0]).toInt(), 
      w : (c.w).toInt(), 
      h : (c.h).toInt()
    };
  },

  getContainCoords : function(){
    var tc = this.trigger.getCoordinates(this.container);
    this.coords.container = {
      y : [tc.top,tc.top+tc.height], 
      x : [tc.left,tc.left+tc.width]
    }; // FIXME
  },
	
  getRelativeOffset : function(){
    this.offset = this.container.getCoordinates();
  },
	
  reset : function(){
    this.detach();
  },

  destroy : function(){
    this.detach();
    this.mask.destroy();
    this.overlay.destroy();
    this.box.destroy();/*
    $H(this.marchingAnts).each(function(el){
      el.destroy();
    });*/
  }
	
});

SEAOLasso.Crop = new Class({

  Extends : SEAOLasso,
	
  options : {
    autoHide : false,
    cropMode : true,
    contain : true,
    handleSize : 8,
    preset : false,
    handleStyle : {
      'border' : '1px solid #000',
      'background-color' : '#ccc' ,
      opacity : .75
    },
    handlePreventsDefault : true
  },
	
  initialize : function(img,options){

    this.img = $(img);
    if( this.img.get('tag') != 'img' ) return false;
		
    var coords = this.img.getCoordinates();

    // the getCoordinates adds 2 extra pixels
    var widthFix = coords.width - 2;
    var heightFix = coords.height - 2;
                
    this.container = new Element('div', {
      'id' :'lassoMask',
      'styles' : {
        'position' : 'relative',
        'width' : widthFix,
        'height' : heightFix       
      }
    }).inject(this.img, 'after');
		
    this.img.setStyle('display', 'none');
    
    options.p = this.container;

    
    this.crop = new Element('img', {
      'src' : this.img.get('src'),
      styles : {
        'position' : 'absolute',
        'top' : 0,
        'left' : 0,
        'width' : widthFix +1,
        'height' : heightFix +1,
        'padding' : 0,
        'margin' : 0,
        'z-index' : this.options.zindex-1
      }
    }).inject(this.container);
    
	
    this.parent(options);
				
    this.binds.handleMove = this.handleMove.bind(this);
    this.binds.handleEnd = this.handleEnd.bind(this);
    this.binds.handles = {};
		
    this.handles = {}; // stores resize handler elements
    // important! this setup a matrix for each handler, patterns emerge when broken into 3x3 grid. Faster/easier processing.
    this.handlesGrid = {
      'NW':[0,0],
      'N':[0,1],
      'NE':[0,2],
      'W':[1,0],
      'E':[1,2],
      'SW':[2,0],
      'S':[2,1],
      'SE':[2,2]
    };
    // this could be more elegant!
    ['NW','N','NE','W','E','SW','S','SE'].each(function(handle){
      var grid = this.handlesGrid[handle]; // grab location in matrix
      this.binds.handles[handle] = this.handleStart.bindWithEvent(this,[handle,grid[0],grid[1]]); // bind
      this.handles[handle] = new Element("div", {
        'styles' : $merge({
          'position' : 'absolute',
          'display' : 'block',
          'visibility' : 'hidden',
          'width' : this.options.handleSize,
          'height' : this.options.handleSize,
          'overflow' : 'hidden',
          'cursor' : (handle.toLowerCase()+'-resize'),
          'z-index' : this.options.zindex+2
        },this.options.handleStyle),
        'events' : {
          'mousedown' : this.binds.handles[handle]
        }
      }).inject(this.box,'bottom');
      // start - Webligo Developments
      // Seems to not let them be hidden for some reason
      this.handles[handle].style.visibility = 'hidden';
    // end - Webligo Developments
    },this);
		
    this.binds.drag = this.handleStart.bindWithEvent(this,['DRAG',1,1]);
    this.overlay.addEvent('mousedown', this.binds.drag);
    
    this.setDefault();
  },
	
  setDefault : function(){
    if(!this.options.preset) return this.resetCoords();
    this.getContainCoords();
    this.getRelativeOffset();
    var c = this.coords.container, d = this.options.preset;
    this.coords.start = {
      x : d[0],
      y : d[1]
    };
    this.active = true;
    this.move({
      page : {
        x: d[2]+this.offset.left,
        y: d[3]+this.offset.top
      }
    });
    this.active = false;
  },
	
  handleStart : function(event,handle,row,col){
    this.currentHandle = {
      'handle' : handle,
      'row' : row,
      'col' : col
    }; // important! used for easy matrix transforms.
    document.addEvents({
      'mousemove' : this.binds.handleMove,
      'mouseup' : this.binds.handleEnd
    });
    // had to merge because we don't want to effect the class instance of box. we want to record it
    event.page.y -= this.offset.top;
    event.page.x -= this.offset.left;
    this.coords.hs = {
      's' : event.page,
      'b' : $merge(this.coords.box)
    }; // handler start (used for 'DRAG')
    this.active = true;

    if( this.options.handlePreventsDefault ) {
      event.stop();
    }
  },
	
  handleMove : function(event){
    var box = this.coords.box, c = this.coords.container, m = event.page, cur = this.currentHandle, s = this.coords.start;
    m.y -= this.offset.top;
    m.x -= this.offset.left;
    if(cur.handle == 'DRAG'){ // messy? could probably be optimized.
      var hs = this.coords.hs, xm = m.x - hs.s.x, ym = m.y - hs.s.y, diff;
      box.y[0] = hs.b.y[0] + ym;
      box.y[1] = hs.b.y[1] + ym;
      box.x[0] = hs.b.x[0] + xm;
      box.x[1] = hs.b.x[1] + xm;
      if((diff = box.y[0] - c.y[0]) < 0) {
        box.y[0] -= diff;
        box.y[1] -= diff;
      } // constrains drag North
      if((diff = box.y[1] - c.y[1]) > 0) {
        box.y[0] -= diff;
        box.y[1] -= diff;
      } // constrains drag South
      if((diff = box.x[0] - c.x[0]) < 0) {
        box.x[0] -= diff;
        box.x[1] -= diff;
      } // constrains drag West
      if((diff = box.x[1] - c.x[1]) > 0) {
        box.x[0] -= diff;
        box.x[1] -= diff;
      } // constrains drag East
      return this.refresh();
    }

    // handles flipping ( nw handle behaves like a se when past the orgin )
    if(cur.row == 0 && box.y[1] < m.y){
      cur.row = 2;
    } 		// fixes North passing South
    if(cur.row == 2 && box.y[0] > m.y){
      cur.row = 0;
    } 		// fixes South passing North
    if(cur.col == 0 && box.x[1] < m.x){
      cur.col = 2;
    } 		// fixes West passing East
    if(cur.col == 2 && box.x[0] > m.x){
      cur.col = 0;
    } 		// fixes East passing West

    if(cur.row == 0 || cur.row == 2){ // if top or bottom row ( center e,w are special case)
      s.y = (cur.row) ? box.y[0] : box.y[1]; 				// set start.y opposite of current direction ( anchor )
      if(cur.col == 0){
        s.x = box.x[1];
      } 				// if West side anchor East
      if(cur.col == 1){
        s.x = box.x[0];
        m.x = box.x[1];
      } // if center lock width
      if(cur.col == 2){
        s.x = box.x[0];
      } 				// if East side anchor West
    }
		
    if(!this.options.ratio){ // these handles only apply when ratios are not in effect. center handles don't makes sense on ratio
      if(cur.row == 1){ // sanity check make sure we are dealing with the right handler
        if(cur.col == 0){
          s.y = box.y[0];
          m.y = box.y[1];
          s.x = box.x[1];
        }		// if West lock height anchor East
        else if(cur.col == 2){
          s.y = box.y[0];
          m.y = box.y[1];
          s.x = box.x[0];
        }// if East lock height anchor West
      }
    }
    m.y += this.offset.top;
    m.x += this.offset.left;
    this.move(event); // now that we manipulated event pass it to move to manage.

    if( this.options.handlePreventsDefault ) {
      event.stop();
    }
  },
	
  handleEnd : function(event){
    document.removeEvents({
      'mousemove' : this.binds.handleMove,
      'mouseup' : this.binds.handleEnd
    });
    this.active = false;
    this.currentHandle = false;
    if(this.options.min && (this.coords.w < this.options.min[0] || this.coords.h < this.options.min[1])){
      if(this.options.preset) this.setDefault();
      else this.resetCoords();
    }

    if( this.options.handlePreventsDefault ) {
      event.stop();
    }
  },
	
  end : function(event){
    if(!this.parent(event)) return false;
    if(this.options.min && (this.coords.w < this.options.min[0] || this.coords.h < this.options.min[1])){
      this.setDefault();
    }
  },
	
  resetCoords : function(){
    this.parent();
    this.coords.box = {
      x : [0,0],
      y : [0,0]
    };
    this.hideHandlers();
  //this.crop.setStyle('clip', 'rect(0px 0px 0px 0px)');
  },	
	
  showHandlers : function(){
    var box = this.coords.box;
		
    if(this.options.min && (this.coords.w < this.options.min[0] || this.coords.h < this.options.min[1])) this.hideHandlers();
		
    else {
      var tops = [], lefts = [], pxdiff = (this.options.handleSize / 2)+1; // used to store location of handlers
      for(var cell = 0, cells = 2; cell <= cells; cell++ ){  // using matrix again
        tops[cell] = ( (cell == 0) ? 0 : ((cell == 2) ? box.y[1] - box.y[0] : (box.y[1] - box.y[0])/2  ) ) - pxdiff;
        lefts[cell] = ( (cell == 0) ? 0 : ((cell == 2) ? box.x[1] - box.x[0] : (box.x[1] - box.x[0])/2 ) ) - pxdiff;
      }

      for(var handleID in this.handlesGrid){ // get each handler's matrix location
        var grid = this.handlesGrid[handleID], handle = this.handles[handleID];
        if(!this.options.ratio || (grid[0] != 1 && grid[1] != 1)){ // if no ratio or not N,E,S,W show
          if(this.options.min && this.options.max){
            if((this.options.min[0] == this.options.max[0]) && (grid[1] % 2) == 0) continue; // turns off W&E since width is set
            if(this.options.min[1] == this.options.max[1] && (grid[0] % 2) == 0) continue;  // turns off N&S since height is set
          }
          handle.setStyles({
            'visibility' : 'visible',
            'top' : tops[grid[0]],
            'left' : lefts[grid[1]]
          }); // just grab from grid
        }
      }
    }
  },
	
  hideHandlers : function(){
    for(handle in this.handles){
      this.handles[handle].setStyle('visibility','hidden');
    }
  },
	
  refresh : function(){
    this.parent();
    var box = this.coords.box, cc = this.coords.container;

    if(Browser.Engine.trident && Browser.Engine.version < 5 && this.currentHandle && this.currentHandle.col === 1)
      this.overlay.setStyle('width' , '100.1%').setStyle('width','100%');

    //  this.crop.setStyle('clip' , 'rect('+(box.y[0])+'px '+(box.x[1])+'px '+(box.y[1])+'px '+(box.x[0])+'px )' );
    this.showHandlers();
  },

  destroy : function(){
    this.parent();
          
    this.img.inject(this.container, 'after');
    this.img.setStyle('display', '');
    this.container.destroy();/*
  $H(this.handles).each(function(el){
    el.destroy();
  });*/
  }

});



var SEAOTagger = new Class({

  Implements : [Events, Options],

  options : {
    // Local options
    'title' : false,
    'description' : false,
    'transImage' : 'application/modules/Core/externals/images/trans.gif',
    'existingTags' : [],
    'tagListElement' : false,
    'linkElement' : false,
    'noTextTagHref' : true,
    'guid' : false,
    'enableCreate' : false,
    'enableDelete' : false,   
    
    // Create
    'createRequestOptions' : {
      'url' : '',
      'data' : {
        'format' : 'json'
      }
    },
    'deleteRequestOptions' : {
      'url' : '',
      'data' : {
        'format' : 'json'
      }
    },

    // Cropper options
    'cropOptions' : {
      'preset' : [10,10,58,58],
      'min' : [48,48],
      'max' : [128,128],
      'handleSize' : 8,
      'opacity' : .6,
      'color' : '#7389AE',
      'border' : 'externals/moolasso/crop.gif'
    },

    // Autosuggest options
    'suggestProto' : 'local',
    'suggestParam' : [
      
    ],
    'suggestOptions' : {
      'minLength': 0,
      'maxChoices' : 100,
      'delay' : 250,
      'selectMode': 'pick',
      //'autocompleteType': 'message',
      'multiple': false,
      'className': 'message-autosuggest',
      'filterSubset' : true,
      'tokenFormat' : 'object',
      'tokenValueKey' : 'label',
      'injectChoice': $empty,
      'onPush' : $empty,
      
      'prefetchOnInit' : true,
      'alwaysOpen' : true,
      'ignoreKeys' : true
    },
    
    'enableShowToolTip':false,
    // Show ToolTip
    'showToolTipRequestOptions' : {
      'url' : '',
      'data' : {
        'format' : 'json'
      }
    }
  },

  initialize : function(el, options) {
    el = $(el);
    if(!el)
      return;
    if( el.get('tag') != 'img' ) {
      this.image = el.getElement('img');
    } else {
      this.image = el;
    }

    this.element = el;
    this.count = 0;
    
    this.actualImage = new Image();
    this.actualImage.src = this.image.get('src');    
    this.setOptions(options);

    //this.element.addEvent('')
    this.options.existingTags.each(this.addTag.bind(this));
    this.addToolTip();
  },

  begin : function() {
    if( !this.options.enableCreate ) return;
    this.getCrop();
    this.getForm();
    this.getSuggest();
    this.fireEvent('onBegin');
  },

  end : function() {
    if( this.crop ) {
      this.crop.destroy();
      delete this.crop;
    }
    if( this.form ) {
      this.form.destroy();
      delete this.form;
    }
    if( this.suggest ) {
      delete this.suggest;
    }
    this.fireEvent('onEnd');
  },

  getCrop : function() {
    if( !this.crop ) {
      var options = $merge(this.options.cropOptions, {
        
        });
      this.crop = new SEAOLasso.Crop(this.image, options);
      this.crop.addEvent('resize', this.onMove.bind(this));
      this.crop.refresh();
    }

    return this.crop;
  },

  getForm : function() {
    if( !this.form ) {
      this.form = new Element('div', {
        'id' : 'tagger_form',
        'class' : 'tagger_form',
        'styles' : {
          'position' : 'absolute',
          'z-index' : '100000',
          'width' : '150px'
        //'height' : '300px'
        }
      }).inject(this.element, 'after');

      // Title
      if( this.options.title ) {
        new Element('div', {
          'class' : 'media_photo_tagform_titlebar',
          'html' : this.options.title
        }).inject(this.form);
      }

      // Container
      this.formContainer = new Element('div', {
        'class' : 'media_photo_tagform_container'
      }).inject(this.form);

      // Description
      if( this.options.description ) {
        new Element('div', {
          'class' : 'media_photo_tagform_text',
          'html' : this.options.description
        }).inject(this.formContainer);
      }

      // Input
      this.input = new Element('input', {
        'id' : 'tagger_input',
        'class' : 'tagger_input',
        'type' : 'text',
        'styles' : {
          
      }
      }).inject(this.formContainer);

      // Choices
      this.choices = new Element('div', {
        'class' : 'tagger_list'
      }).inject(this.formContainer);

      // Submit container
      var submitContainer = new Element('div', {
        'class' : 'media_photo_tagform_submits'
      }).inject(this.formContainer);

      var self = this;
      new Element('a', {
        'id' : 'tag_save',
        'href' : 'javascript:void(0);',
        'html' : en4.core.language.translate('Save'),
        'events' : {
          'click' : function() {
            var data = {}; //JSON.decode(choice.getElement('input').value);
            data.label = self.input.value;
            if( $type(data.label) && data.label != '' ) {
              data.extra = self.coords;
              self.createTag(data);
            }
          }
        }
      }).inject(submitContainer);

      new Element('a', {
        'id' : 'tag_cancel',
        'href' : 'javascript:void(0);',
        'html' : en4.core.language.translate('Cancel'),
        'events' : {
          'click' : function() {
            this.end();
          }.bind(this)
        }
      }).inject(submitContainer);

      this.input.focus();
    }
    
    return this.form;
  },

  getSuggest : function() {
    if( !this.suggest ) {
      var self = this;
      var options = $merge(this.options.suggestOptions, {
        'overflow' : true,
        'maxChoices' : 4,
        'customChoices' : this.choices,
        'injectChoice' : function(token) {
          var choice = new Element('li', {
            'class': 'autocompleter-choices',
            //'value': token.id,
            'html': token.photo || '',
            'id': token.guid
          });
          new Element('div', {
            'html' : this.markQueryValue(token.label),
            'class' : 'autocompleter-choice'
          }).inject(choice);
          new Element('input', {
            'type' : 'hidden',
            'value' : JSON.encode(token)
          }).inject(choice);
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        },
        'onChoiceSelect' : function(choice) {
          var data = JSON.decode(choice.getElement('input').value);
          data.extra = self.coords;
          self.createTag(data);
        //alert(choice.getElement('input').value);
        },
        'emptyChoices' : function() {
          this.fireEvent('onHide', [this.element, this.choices]);
        },
        'onCommand' : function(e) {
          switch (e.key) {
            case 'enter':
              self.createTag({
                label : self.input.value,
                extra : self.coords
              });
              break;
          }
        }
      });

      if( this.options.suggestProto == 'local' ) {
        this.suggest = new Autocompleter.Local(this.input, this.options.suggestParam, options);
      } else if( this.options.suggestProto == 'request.json' ) {
        this.suggest = new Autocompleter.Request.JSON(this.input, this.options.suggestParam, options);
      }
    }

    return this.suggest;
  },

  getTagList : function() {
    if( !this.tagList ) {
      if( !this.options.tagListElement ) {
        this.tagList = new Element('div', {
          'class' : 'tag_list'
        }).inject(this.element, 'after');
      } else {
        this.tagList = $(this.options.tagListElement);
      }
    }

    return this.tagList;
  },

  onMove : function(coords) {
    this.coords = coords;
    var coords_y=coords.y;
    var pos = {
      x:0,
      y:0
    }; //this.element.getPosition();
    var form = this.getForm();
    var formParentHeight=this.getForm().getParent().getCoordinates().height - 2;
    if(formParentHeight < (coords_y + 20 +form.getCoordinates().height - 2  )){
      coords_y = coords_y - (form.getCoordinates().height - 2);    
    }
    form.setStyles({
      'top' : pos.y + coords_y + 20,
      'left' : pos.x + coords.x + coords.w + 20
    });
  },




  // Tagging stuff

  addTag : function(params) {
    // Required: id, text, x, y, w, h

    if ( 'object' != $type(params)  || !params.extra) {
      //alert('This entry has already been tagged.');
      return;
    }

    var baseX = 0, baseY = 0, baseW = 0, baseH = 0;
    
    ["x", "y", "w", "h"].each(function(key) {
      params.extra[key] = parseInt(params.extra[key]);
    });
    
    var actualImageCoords =this.getActualImageCoords();
    var imageCoords =this.getImageCoords();
    // Set Relative X Coords    
    ["x", "w"].each(function(key) {
      params.extra[key]= (params.extra[key]/ actualImageCoords.width * imageCoords.width).toInt();    
    });
    
    // Set Relative Y Coords     
    ["y", "h"].each(function(key) {
      params.extra[key]= (params.extra[key] / actualImageCoords.height * imageCoords.height).toInt();
    });

    if( this.options.noTextTagHref && params.tag_type == 'core_tag' ) {
      delete params.href;
    }
   
    // Make tag
    if($('tag_' + params.id))
      $('tag_' + params.id).destroy();
    var tag = new Element('div', {
      'id' : 'tag_' + params.id,
      'class' : 'tag_div',
      'html' : '<img src="'+this.options.transImage+'" width="100%" height="100%" />',
      'styles' : {
        'position' : 'absolute',
        'width' : params.extra.w,
        'height' : params.extra.h,
        'top' : baseY + params.extra.y,
        'left' : baseX + params.extra.x
      },
      'events' : {
        'mouseover' : function() {
          this.showTag(params.id);
        }.bind(this),
        'mouseout' : function() {
          this.hideTag(params.id);
        }.bind(this)
      }
    }).inject(this.element, 'after');

    // Make label
    // Note: we need to use visibility hidden to position correctly in IE
    if($('tag_label_' + params.id))
      $('tag_label_' + params.id).destroy();
    var label = new Element("span", {
      'id' : 'tag_label_' + params.id,
      'class' : 'tag_label',
      'html' : params.text,
      'styles' : {
        'position' : 'absolute'
      }
    }).inject(this.element, 'after');

    var labelPos = {};
    labelPos.top = ( baseY + params.extra.y + tag.getSize().y );
    labelPos.left = Math.round( ( baseX + params.extra.x ) + ( tag.getSize().x / 2 ) - (label.getSize().x / 2) );

    if( this.element.getSize().y < labelPos.top.toInt() + 20 ){
      labelPos.top = baseY + params.extra.y - label.getSize().y;
    }

    label.setStyles(labelPos);

    this.hideTag(params.id);

    var isFirst = ( !$type(this.count) || this.count == 0 );
    this.getTagList().setStyle('display', '');

    // Make list
    if($('tag_comma_' + params.id))
      $('tag_comma_' + params.id).destroy();
    if( !isFirst ) new Element('span', {
      'id' : 'tag_comma_' + params.id,
      'class' : 'tag_comma',
      'html' : ','
    }).inject(this.getTagList());

    // Make other thingy
    if($('tag_info_' + params.id))
      $('tag_info_' + params.id).destroy();
    var info = new Element('span', {
      'id' : 'tag_info_' + params.id,
      'class' : 'tag_info media_tag_listcontainer'
    }).inject(this.getTagList());
    
    if($('tag_activator_' + params.id))
      $('tag_activator_' + params.id).destroy();
    var activator = new Element('a', {
      'id' : 'tag_activator_' + params.id,
      'class' : 'tag_activator',
      'href' : params.href || null,
      'html' : params.text,
      'rel': params.id,
      'events' : {
        'mouseover' : function() {
          this.showTag(params.id);       
        }.bind(this),
        'mouseout' : function() {
          this.hideTag(params.id);
        }.bind(this)
      }
    }).inject(info);
   
    // Delete
    if(!this.options.enableShowToolTip  && this.checkCanRemove(params.id) )
    {
      info.appendText(' (');
      if($('tag_destroyer_' + params.id))
        $('tag_destroyer_' + params.id).destroy();
      var destroyer = new Element('a', {
        'id' : 'tag_destroyer_' + params.id,
        'class' : 'tag_destroyer albums_tag_delete',
        'href' : 'javascript:void(0);',
        'html' : en4.core.language.translate('delete'),
        'events' : {
          'click' : function() {
            this.removeTag(params.id);            
          }.bind(this)
        }
      }).inject(info);
      info.appendText(')');
    }
    
    this.count++;
  },

  createTag : function(params) {
    if( !this.options.enableCreate ) return;
    
    params.extra = this.getSaveCoords(params.extra);
    // Send request
    var requestOptions = $merge(this.options.createRequestOptions, {
      'data' : $merge(params, {
        
        }),
      'onComplete' : function(responseJSON) {
        this.addTag(responseJSON);
        this.addToolTip();
        this.fireEvent('onCreateTag',responseJSON);
      }.bind(this)
    });
    var request = new Request.JSON(requestOptions);
    request.send();

    // End tagging
    this.end();
  },

  removeTag : function(id) {

    if( !this.checkCanRemove(id) ) return;

    // Remove from frontend
    var next = $('tag_info_' + id).getNext(); 
    if($('tag_comma_' + id))
      $('tag_comma_' + id).destroy();
    else if( next && next.get('html').trim() == ',' ) next.destroy();
    $('tag_' + id).destroy();
    $('tag_label_' + id).destroy();
    $('tag_info_' + id).destroy();
    this.count--;
    this.fireEvent('onRemoveTag',[id]);
    
    // Send request
    var requestOptions = $merge(this.options.deleteRequestOptions, {
      'data' : {
        'tagmap_id' : id
      },
      'onComplete' : function(responseJSON) {
        
      }.bind(this)
    });
    var request = new Request.JSON(requestOptions);
    request.send();    
  },

  checkCanRemove : function(id) {

    // Check if can remove
    var tagData;
    this.options.existingTags.each(function(datum) {
      if( datum.tagmap_id == id ) {
        tagData = datum;
      }
    });

    if( this.options.enableDelete ) return true;

    if( tagData ) {
      if( tagData.tag_type + '_' + tagData.tag_id == this.options.guid ) return true;
      if( tagData.tagger_type + '_' + tagData.tagger_id == this.options.guid ) return true;
    }
    
    return false;
  },

  showTag : function(id) {
    $('tag_' + id)/*.addClass('tag_div')*/.removeClass('tag_div_hidden');
    $('tag_label_' + id)/*.addClass('tag_label')*/.removeClass('tag_label_hidden');
  },

  hideTag : function(id) {
    $('tag_' + id).addClass('tag_div_hidden')/*.removeClass('tag_div')*/;
    $('tag_label_' + id).addClass('tag_label_hidden')/*.removeClass('tag_label')*/;
  },
  
  getActualImageCoords: function(){
    return {
      'width' : this.actualImage.width, 
      'height' : this.actualImage.height
    };
  },
  
  
  getImageCoords: function(){   
    var coords = this.image.getCoordinates();    
    // the getCoordinates adds 2 extra pixels
    return {
      'width' : (coords.width - 2), 
      'height' : (coords.height - 2)
    };
  },
  getCropCoords: function(){
    var coords =this.getCrop().crop.getCoordinates();
    // the getCoordinates adds 2 extra pixels
    return {
      'width' : (coords.width - 2), 
      'height' : (coords.height - 2)
    };
  },
  getSaveCoords: function(coods){
    var actualImageCoords =this.getActualImageCoords();
    var imageCoords =this.getCropCoords();
    // Set Relative X Coords    
    ["x", "w"].each(function(key) {      
      coods[key]= ((coods[key]/ imageCoords.width) * actualImageCoords.width).toInt();     
    });
    
    // Set Relative Y Coords     
    ["y", "h"].each(function(key) {
      coods[key]= ((coods[key] / imageCoords.height) * actualImageCoords.height).toInt();
    });
   
    return coods;
  },

  setToolTip:function(){
    if(this.options.enableShowToolTip == false)
      return;
    // Add tooltips
    var window_size = window.getSize()
    return  new SEATips($$('.tag_activator'), {
      fixed : true,
      title:'',
      className : 'sea_add_tooltip_link_tips',
      hideDelay :200,
      showDelay :200,
      offset : {
        'x' : 0,
        'y' : 0
      },
      windowPadding: {
        'x':200, 
        'y':(window_size.y/2)
      },
      req_pendding:0
    }   
    ); 
  },
  getToolTipDefaultContent: function(){
    var toolTipDefault = new Element('div', {});
     
    var info_tip = new Element('div', {
     
      'class' : 'uiOverlay info_tip',    
      'styles' : {    
        'width' : 200,    
        'top' : 0        
      }      
    }).inject(toolTipDefault);
    
    var info_tip_content_wrapper = new Element('div', {
      'class' : 'info_tip_content_wrapper'
    }).inject(info_tip);
   
    var info_tip_content = new Element('div', {
      'class' : 'info_tip_content'
    }).inject(info_tip_content_wrapper);
   
    new Element('div', {     
      'class' : 'info_tip_content_loader',
      'html' : '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />'          
    }).inject(info_tip_content);
    
    
    return toolTipDefault;
  },
  addToolTip:function(){
    var self=this;
    if(this.options.enableShowToolTip){
      $$('.tag_activator').addEvent('mouseover', function(event) { 
        var el = $(event.target);   
        ItemTooltips.options.offset.y = el.offsetHeight;
        ItemTooltips.options.showDelay = 0; 
        ItemTooltips.options.showToolTip=true;
        if( !el.retrieve('tip-loaded', false) ) {
          ItemTooltips.options.req_pendding++;
          var id='';
          if(el.hasAttribute("rel"))
            id=el.rel;
          if(id =='')
            return;

          el.store('tip-loaded', true);
          el.store('tip:title',self.getToolTipDefaultContent());
          el.store('tip:text', ''); 

          el.addEvent('mouseleave',function(){
            ItemTooltips.options.showToolTip=false;  
          });       

          var requestOptions = $merge(self.options.showToolTipRequestOptions, {
            'data' : {
              format : 'html',
              'tagmap_id' : id
            },
            evalScripts : true,
            onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {          
              el.store('tip:title', '');
              var responseHTMLContent = new Element('div',
              {
                html :responseHTML    
              });

              if( self.checkCanRemove(id) )
              {
                var responseHTMLContentDelete = responseHTMLContent.getElement('.tagged_info_tip_content_delete');
                responseHTMLContentDelete.appendText(' (');
                if($('tag_destroyer_' + id))
                  $('tag_destroyer_' + id).destroy();
                new Element('a', {
                  'id' : 'tag_destroyer_' + id,
                  'class' : 'tag_destroyer albums_tag_delete',
                  'href' : 'javascript:void(0);',
                  'html' : en4.core.language.translate('remove tag'),
                  'events' : {
                    'click' : function() {
                      ItemTooltips.options.canHide = true;
                      ItemTooltips.hide(el);
                      self.removeTag(id);                                            
                    }.bind(this)
                  }
                }).inject(responseHTMLContentDelete);
                responseHTMLContentDelete.appendText(')');
              }
              
              el.store('tip:text', responseHTMLContent);
              ItemTooltips.options.showDelay=0;
              ItemTooltips.elementEnter(event, el); // Force it to update the text
              ItemTooltips.options.showDelay=200;
              ItemTooltips.options.req_pendding--;
              if(!ItemTooltips.options.showToolTip || ItemTooltips.options.req_pendding > 0){
                ItemTooltips.elementLeave(event,el);
              }           
              var tipEl=ItemTooltips.toElement();
              tipEl.addEvents({
                'mouseenter': function() {
                  ItemTooltips.options.canHide = false;
                  ItemTooltips.show(el);
                },
                'mouseleave': function() {                
                  ItemTooltips.options.canHide = true;
                  ItemTooltips.hide(el);                    
                }
              });
              Smoothbox.bind($$(".tag_activator"));
            }.bind(this)
          });
          
          new Request.HTML(requestOptions).send();
        }

      });
      var ItemTooltips = self.setToolTip();
    }  
  }

});