
var SEAOMooVerticalScroll = new Class({

  Implements: [Events, Options],

  options: {
    maxThumbSize: 15,
    wheel: 40  
  },

  initialize: function(main, content, options){
    this.setOptions(options);
			
    this.main = $(main);
    this.content = $(content);			
				
    this.scrollbar = new Element('div', {
      'class': 'verticalScroll'
    }).injectAfter(this.content);				

    this.track = new Element('div', {
      'class': 'verticalTrack'
    }).injectInside(this.scrollbar);

    this.thumb = new Element('div', {
      'class': 'verticalThumb'
    }).injectInside(this.track);

    this.thumbTop = new Element('div', { /* +kh */
      'class': 'verticalThumbTop'
    }).injectInside(this.thumb); /* kh (this.track); */


    this.thumbBot = new Element('div', { /* +kh */
      'class': 'verticalThumbBottom'
    }).injectInside(this.thumb); /* kh (this.track); */

					
    this.bound = {
      'update': this.update.bind(this),
      'start': this.start.bind(this),			
      'end': this.end.bind(this),
      'drag': this.drag.bind(this),			
      'wheel': this.wheel.bind(this),
      'page': this.page.bind(this)			
    };

    this.position = {};	
    this.mouse = {};		
    this.update();
    this.attach();
  },

  update: function(){
			
    this.main.setStyle('height', this.content.offsetHeight);
    this.track.setStyle('height', this.content.offsetHeight);		
		      
    // Remove and replace vertical scrollbar			
    if (this.content.scrollHeight <= this.main.offsetHeight) {
      this.scrollbar.setStyle('display', 'none');								
    } else {
      this.scrollbar.setStyle('display', 'block');			
    }
			
    // Vertical
			
    this.contentHeight = this.content.offsetHeight;
    this.contentScrollHeight = this.content.scrollHeight;
    this.trackHeight = this.track.offsetHeight;
    if(this.contentScrollHeight <=0)
      this.contentScrollHeight=1;
    this.ContentHeightRatio = this.contentHeight / this.contentScrollHeight;

    this.thumbSize = (this.trackHeight * this.ContentHeightRatio).limit(this.options.maxThumbSize, this.trackHeight);

    this.scrollHeightRatio = this.contentScrollHeight / this.trackHeight;

    this.thumb.setStyle('height', this.thumbSize);

    this.updateThumbFromContentScroll();
    this.updateContentFromThumbPosition();
			
  },

  updateContentFromThumbPosition: function(){
    this.content.scrollTop = this.position.now * this.scrollHeightRatio;
  },	
	

  updateThumbFromContentScroll: function(){
    this.position.now = (this.content.scrollTop / this.scrollHeightRatio).limit(0, (this.trackHeight - this.thumbSize));
    this.thumb.setStyle('top', this.position.now);
    this.fireEvent('seaoMooVerticalScrool',this);
  },		
          

  attach: function(){
    
    this.content.addEvents({ mouseenter: this.bound.update,
      mouseleave: this.bound.update });    
    this.thumb.addEvent('mousedown', this.bound.start);
    if (this.options.wheel) this.content.addEvent('mousewheel', this.bound.wheel);
    this.track.addEvent('mouseup', this.bound.page);		
					
  },
		
  wheel: function(event){
    this.content.scrollTop -= event.wheel * this.options.wheel;
    this.updateThumbFromContentScroll();
    event.stop();
  },

  page: function(event){
    if (event.page.y > this.thumb.getPosition().y) this.content.scrollTop += this.content.offsetHeight;
    else this.content.scrollTop -= this.content.offsetHeight;
    this.updateThumbFromContentScroll();
    event.stop();
  },
  scrollTop : function(){
    this.content.scrollTop =0;
    this.updateThumbFromContentScroll();
  },		

  start: function(event){
    this.mouse.start = event.page.y;
    this.position.start = this.thumb.getStyle('top').toInt();
    document.addEvent('mousemove', this.bound.drag);
    document.addEvent('mouseup', this.bound.end);
    this.thumb.addEvent('mouseup', this.bound.end);
    event.stop();
  },		
	
  end: function(event){
    document.removeEvent('mousemove', this.bound.drag);					
    document.removeEvent('mouseup', this.bound.end);
    this.thumb.removeEvent('mouseup', this.bound.end);		
    event.stop();
  },

  drag: function(event){
    this.mouse.now = event.page.y;
    this.position.now = (this.position.start + (this.mouse.now - this.mouse.start)).limit(0, (this.trackHeight - this.thumbSize));
    this.updateContentFromThumbPosition();
    this.updateThumbFromContentScroll();
    event.stop();
  }	
});