var SEAOMooHorizontalScrollBar = new Class({
 
  Implements: [Events, Options],
 
  options: {
    maxThumbSize: 15,
         
    arrows: true,
    horizontalScroll: true, // horizontal scrollbars
    horizontalScrollBefore :true
  },
 
  initialize: function(main, content, options){
    this.setOptions(options);
 
    this.main = $(main);
    this.content = $(content);
 
    if (this.options.arrows == true){
      this.arrowOffset = 30;
    } else {
      this.arrowOffset = 0;
    }
 
    if (this.options.horizontalScroll == true){
      this.horizontalScrollOffset = 15;
    } else {
      this.horizontalScrollOffset = 0;
    }
    if(this.options.horizontalScrollElement){
      this.horizontalScrollbar = new Element('div', {
        'class': 'horizontalScrollbar'
      }).injectInside($(this.options.horizontalScrollElement));
    }else{
      this.horizontalScrollbar = new Element('div', {
        'class': 'horizontalScrollbar'
      }).injectAfter(this.content);
    }
    if (this.options.arrows == true){
      this.arrowLeft = new Element('div', {
        'class': 'arrowLeft'
      }).injectInside(this.horizontalScrollbar);
    }
 
    this.horizontalTrack = new Element('div', {
      'class': 'horizontalTrack'
    }).injectInside(this.horizontalScrollbar);
 
    this.horizontalThumb = new Element('div', {
      'class': 'horizontalThumb'
    }).injectInside(this.horizontalTrack);
 
    if (this.options.arrows == true){
      this.arrowRight = new Element('div', {
        'class': 'arrowRight'
      }).injectInside(this.horizontalScrollbar);
    }
 
 
    if(this.options.horizontalScrollBefore==true){
      this.horizontalScrollbarBefore = new Element('div', {
        'class': 'horizontalScrollbar'
      }).injectInside($(this.options.horizontalScrollBeforeElement));
 
      if (this.options.arrows == true){
        this.arrowLeftBefore = new Element('div', {
          'class': 'arrowLeft'
        }).injectInside(this.horizontalScrollbarBefore);
      }
 
      this.horizontalTrackBefore = new Element('div', {
        'class': 'horizontalTrack'
      }).injectInside(this.horizontalScrollbarBefore);
 
      this.horizontalThumbBefore = new Element('div', {
        'class': 'horizontalThumb'
      }).injectInside(this.horizontalTrackBefore);
 
      if (this.options.arrows == true){
        this.arrowRightBefore = new Element('div', {
          'class': 'arrowRight'
        }).injectInside(this.horizontalScrollbarBefore);
      }
    }
           
 
    this.bound = {
              
      'horizontalStart': this.horizontalStart.bind(this),
      'end': this.end.bind(this),
              
      'horizontalDrag': this.horizontalDrag.bind(this),
            
               
      'horizontalPage': this.horizontalPage.bind(this)
    };
 
   
    this.horizontalPosition = {};
         
    this.horizontalMouse = {};
    this.update();
    this.attach();
  },
 
  update: function(){

        
 
    this.main.setStyle('width', this.content.offsetWidth + 15);
 
    this.horizontalTrack.setStyle('width', this.content.offsetWidth - this.arrowOffset);
    if (this.options.horizontalScrollBefore == true){
      this.horizontalTrackBefore.setStyle('width', this.content.offsetWidth - this.arrowOffset);
    }
          

 
    // Remove and replace horizontal scrollbar
    if (this.content.scrollWidth <= this.main.offsetWidth) {
      this.horizontalScrollbar.setStyle('display', 'none');
      if (this.options.horizontalScrollBefore == true){
        this.horizontalScrollbarBefore.setStyle('display', 'none');
      }  
      if(!this.options.horizontalScrollElement){
      this.content.setStyle('height', this.content.offsetHeight + this.horizontalScrollOffset);
      }
    } else {
      this.horizontalScrollbar.setStyle('display', 'block');
      if (this.options.horizontalScrollBefore == true){
        this.horizontalScrollbarBefore.setStyle('display', 'block');
      }         
    }
 
              
    // Horizontal
 
    this.horizontalContentSize = this.content.offsetWidth;
    this.horizontalContentScrollSize = this.content.scrollWidth;
    this.horizontalTrackSize = this.horizontalTrack.offsetWidth;
 
    this.horizontalContentRatio = this.horizontalContentSize / this.horizontalContentScrollSize;
 
    this.horizontalThumbSize = (this.horizontalTrackSize * this.horizontalContentRatio).limit(this.options.maxThumbSize, this.horizontalTrackSize);
 
    this.horizontalScrollRatio = this.horizontalContentScrollSize / this.horizontalTrackSize;
 
    this.horizontalThumb.setStyle('width', this.horizontalThumbSize);
    if (this.options.horizontalScrollBefore == true){        
      this.horizontalThumbBefore.setStyle('width', this.horizontalThumbSize);
    }         
    this.horizontalUpdateThumbFromContentScroll();
    this.horizontalUpdateContentFromThumbPosition();     
  },
 
      
  horizontalUpdateContentFromThumbPosition: function(){
    this.content.scrollLeft = this.horizontalPosition.now * this.horizontalScrollRatio;
  },
 
    
 
  horizontalUpdateThumbFromContentScroll: function(){
    this.horizontalPosition.now = (this.content.scrollLeft / this.horizontalScrollRatio).limit(0, (this.horizontalTrackSize - this.horizontalThumbSize));
    this.horizontalThumb.setStyle('left', this.horizontalPosition.now);
    if (this.options.horizontalScrollBefore == true){   
      this.horizontalThumbBefore.setStyle('left', this.horizontalPosition.now);
    }
  },
 
  attach: function(){
          
    this.horizontalThumb.addEvent('mousedown', this.bound.horizontalStart);
    this.horizontalTrack.addEvent('mouseup', this.bound.horizontalPage);
 
    if (this.options.arrows == true){
              
              
      this.arrowLeft.addEvent('mousedown', function(event){
        this.interval = (function(event){
          this.content.scrollLeft -= this.options.wheel;
          this.horizontalUpdateThumbFromContentScroll();
        }.bind(this).periodical(40))
      }.bind(this));
 
      this.arrowLeft.addEvent('mouseup', function(event){
        $clear(this.interval);
      }.bind(this));
 
      this.arrowLeft.addEvent('mouseout', function(event){
        $clear(this.interval);
      }.bind(this));
 
      this.arrowRight.addEvent('mousedown', function(event){
        this.interval = (function(event){
          this.content.scrollLeft += this.options.wheel;
          this.horizontalUpdateThumbFromContentScroll();
        }.bind(this).periodical(40))
      }.bind(this));
 
      this.arrowRight.addEvent('mouseup', function(event){
        $clear(this.interval);
      }.bind(this));
 
      this.arrowRight.addEvent('mouseout', function(event){
        $clear(this.interval);
      }.bind(this));
    }
    if (this.options.horizontalScrollBefore == true){ 
      this.horizontalThumbBefore.addEvent('mousedown', this.bound.horizontalStart);
      this.horizontalTrackBefore.addEvent('mouseup', this.bound.horizontalPage);
 
      if (this.options.arrows == true){
              
              
        this.arrowLeftBefore.addEvent('mousedown', function(event){
          this.interval = (function(event){
            this.content.scrollLeft -= this.options.wheel;
            this.horizontalUpdateThumbFromContentScroll();
          }.bind(this).periodical(40))
        }.bind(this));
 
        this.arrowLeftBefore.addEvent('mouseup', function(event){
          $clear(this.interval);
        }.bind(this));
 
        this.arrowLeftBefore.addEvent('mouseout', function(event){
          $clear(this.interval);
        }.bind(this));
 
        this.arrowRightBefore.addEvent('mousedown', function(event){
          this.interval = (function(event){
            this.content.scrollLeft += this.options.wheel;
            this.horizontalUpdateThumbFromContentScroll();
          }.bind(this).periodical(40))
        }.bind(this));
 
        this.arrowRightBefore.addEvent('mouseup', function(event){
          $clear(this.interval);
        }.bind(this));
 
        this.arrowRightBefore.addEvent('mouseout', function(event){
          $clear(this.interval);
        }.bind(this));
      } 
    }
  },
 
      
      
       
 
  horizontalPage: function(event){
    if (event.page.x > this.horizontalThumb.getPosition().x) this.content.scrollLeft += this.content.offsetWidth;
    else this.content.scrollLeft -= this.content.offsetWidth;
    this.horizontalUpdateThumbFromContentScroll();
    event.stop();
  },
 
     
 
  horizontalStart: function(event){
    this.horizontalMouse.start = event.page.x;
    this.horizontalPosition.start = this.horizontalThumb.getStyle('left').toInt();
    document.addEvent('mousemove', this.bound.horizontalDrag);
    document.addEvent('mouseup', this.bound.end);
    this.horizontalThumb.addEvent('mouseup', this.bound.end);
    if (this.options.horizontalScrollBefore == true){ 
      this.horizontalThumbBefore.addEvent('mouseup', this.bound.end);
    }
    event.stop();
  },
 
  end: function(event){
          
    document.removeEvent('mousemove', this.bound.horizontalDrag);
    document.removeEvent('mouseup', this.bound.end);
          
    this.horizontalThumb.removeEvent('mouseup', this.bound.end);
    if (this.options.horizontalScrollBefore == true){ 
      this.horizontalThumbBefore.removeEvent('mouseup', this.bound.end);
    }
    event.stop();
  },
 
       
  horizontalDrag: function(event){
    this.horizontalMouse.now = event.page.x;
    this.horizontalPosition.now = (this.horizontalPosition.start + (this.horizontalMouse.now - this.horizontalMouse.start)).limit(0, (this.horizontalTrackSize - this.horizontalThumbSize));
    this.horizontalUpdateContentFromThumbPosition();
    this.horizontalUpdateThumbFromContentScroll();
    event.stop();
  }
 
});