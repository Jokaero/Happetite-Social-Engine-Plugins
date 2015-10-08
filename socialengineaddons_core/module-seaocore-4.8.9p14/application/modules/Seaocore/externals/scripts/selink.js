/* $Id: composer_selink.js 2014-18-01 00:00:00Z SocialEngineAddOns Copyright 2014-2015 BigStep
 Technologies Pvt. Ltd. $*/

var SEAOLink;

(function() {

var $ = 'id' in document ? document.id : window.$;

SEAOLink = new Class({

  params : {},
	
	elements : {},
	
  options : {
    title : 'Add Link',
    lang : {},
    // Options for the link preview request
    requestOptions : {
			url: en4.core.baseUrl + 'core/link/preview'
		},
    // Various image filtering options
    imageMaxAspect : ( 10 / 3 ),
    imageMinAspect : ( 3 / 10 ),
    imageMinSize : 48,
    imageMaxSize : 5000,
    imageMinPixels : 2304,
    imageMaxPixels : 1000000,
    imageTimeout : 5000,
    // Delay to detect links in input
    monitorDelay : 600,
		loadingImage : en4.core.staticBaseUrl + 'application/modules/Core/externals/images/loading.gif',
    debug : false
  },
	
  initialize : function(options) {
    this.params = new Hash(this.params);
		this.elements = new Hash(this.elements);
  },
	
	setLinkName:  function(value) {
		if(value == 1) {
			this.activate();
		} else {
			if(document.getElementById('seao_link_form_wrapper')) {
				document.getElementById('seao_link_form_wrapper').remove();
				document.getElementById('title').value = '';
				if(document.getElementById('description'))
					document.getElementById('description').value = '';
				if(document.getElementById('body'))
					document.getElementById('body').value = '';
			}
		}
	},
	
	cancelLink:  function() {
		if(document.getElementById('seao_link_form_wrapper')) {
			document.getElementById('seao_link_form_wrapper').remove();
			document.getElementById('title').value = '';
			if(document.getElementById('description'))
				document.getElementById('description').value = '';
			if(document.getElementById('body'))
				document.getElementById('body').value = '';
			document.getElementById('link_photo').value = '';
			this.activate();
		}
	},
	
  activate : function() {

    if( this.active ) return;

    // Generate form
		var formElements = document.getElementById('title-wrapper');
		
		if(!document.getElementById('seao_link_form_wrapper')) {			
			var temp_div = new Element('div', {
					'id' : 'seao_link_form_wrapper',
					'class' : 'form-wrapper seao_link_form_wrapper'
			}).inject(formElements, 'before');
			
			var temp_div_label = new Element('div', {
				'class' : 'form-label'
			}).inject(temp_div);
			
			var label = new Element('label', {
				'for' : 'seao_link_label',
				'id' : 'seao_link_label',
				'html' : this._lang('Enter URL of Website'),
			}).inject(temp_div_label);
			
			var temp_div_element = new Element('div', {
				'class' : 'form-element'
			}).inject(temp_div);
			
			var input =  new Element('input', {
				'id' : 'seao_link_form_input',
				'class' : 'form-input',
				'autocomplete' : 'off',
				'type' : 'text'
			}).inject(temp_div_element);

			formElements.formSubmit = new Element('button', {
				'id' : 'seao_link_form_submit',
				'class' : 'form-submit',
				'html' : 'Attach',
				'events' : {
					'click' : function(e) {
						e.stop();
						this.doAttach();
					}.bind(this)
				}
			}).inject(temp_div_element);
		}
		
		if(!document.getElementById('seaolink_body')) {
			var tray = document.getElementById('seao_link_form_submit');
			this.elements.body = new Element('div', {
				'id' : 'seaolink_body',
				'class' : 'seaolink-body'
			}).inject(temp_div_element);
		}
		
		formElements.formSubmit = new Element('button', {
			'id' : 'seao_link_form_cancel',
			'class' : 'form-submit',
			'html' : 'Cancel',
			'style': 'display:none',
			'events' : {
				'click' : function(e) {
					e.stop();
					this.cancelLink();
				}.bind(this)
			}
		}).inject(this.elements.body, 'after');
  },

	makeLoading : function(action) {
		if( !document.getElementById('seao_link_loading')) {
			if( action == 'empty' ) {
				this.elements.body.empty();
			} else if( action == 'hide' ) {
				this.elements.body.getChildren().each(function(element){ element.setStyle('display', 'none')});
			} else if( action == 'invisible' ) {
				this.elements.body.getChildren().each(function(element){
        element.setStyle('height', '0px').setStyle('visibility', 'hidden');
        });
			}
			
			this.elements.loading = new Element('div', {
				'id' : 'seao_link_loading',
				'class' : 'seao-loading'
			}).inject(this.elements.body);
			
			var image = this.elements.loadingImage || (new Element('img', {
				'id' : 'seao-loading-image',
				'class' : 'seao-loading-image'
			}));
			
			image.inject(this.elements.loading);
			
			new Element('span', {
				'html' : this._lang('Loading...')
			}).inject(this.elements.loading);
		}
	},
	
  doAttach : function() {
		var val = document.getElementById('seao_link_form_input').value;
    if( !val ) {
      return;
    }
    if( !val.match(/^[a-zA-Z]{1,5}:\/\//) )
    {
      val = 'http://' + val;
    }
    this.params.set('uri', val);
    // Input is empty, ignore attachment
    if( val == '' ) {
      e.stop();
      return;
    }

    // Send request to get attachment
    var options = $merge({
      'data' : {
        'format' : 'json',
        'uri' : val
      },
      'onComplete' : this.doProcessResponse.bind(this)
    }, this.options.requestOptions);

    // Inject loading
    this.makeLoading('empty');

    // Send request
    this.request = new Request.JSON(options);
    this.request.send();
  },

	doProcessResponse : function(responseJSON, responseText) {
    // Handle error
    if( $type(responseJSON) != 'object' ) {
      responseJSON = {
        'status' : false
      };
    }
    this.params.set('uri', responseJSON.url);

    // If google docs then just output Google Document for title and descripton
    var uristr = responseJSON.url;
    if (uristr.substr(0, 23) == 'https://docs.google.com') {
      var title = uristr;
      var description = 'Google Document';
    } else {
      var title = responseJSON.title || responseJSON.url;
      var description = responseJSON.description || responseJSON.title || responseJSON.url;
    }
       
    var images = responseJSON.images || [];

    this.params.set('title', title);
    this.params.set('description', description);
    this.params.set('images', images);
    this.params.set('loadedImages', []);
    this.params.set('thumb', '');

    if( images.length > 0 ) {
      this.doLoadImages();
    } else {
      this.doShowPreview();
    }
    
    if(document.getElementById('seao_link_form_input'))
			document.getElementById('seao_link_form_input').style.display = 'none';
		if(document.getElementById('seao_link_label'))
			document.getElementById('seao_link_label').style.display = 'none';
		if(document.getElementById('seao_link_form_submit'))
			document.getElementById('seao_link_form_submit').style.display = 'none';
  },
  
  // Image loading
  doLoadImages : function() {
    // Start image load timeout
    var interval = (function() {
      // Debugging
      if( this.options.debug ) {
        console.log('Timeout reached');
      }
      this.doShowPreview();
    }).delay(this.options.imageTimeout, this);
      
    // Load them images
    this.params.loadedImages = [];

    this.params.set('assets', new Asset.images(this.params.get('images'), {
      'properties' : {
        'class' : 'compose-link-image'
      },
      'onProgress' : function(counter, index) {
        this.params.loadedImages[index] = this.params.images[index];
        // Debugging
        if( this.options.debug ) {
          console.log('Loaded - ', this.params.images[index]);
        }
      }.bind(this),
      'onError' : function(counter, index) {
        delete this.params.images[index];
      }.bind(this),
      'onComplete' : function() {
        $clear(interval);
        this.doShowPreview();
      }.bind(this)
    }));
  },


  // Preview generation
  
  doShowPreview : function() {
    var self = this;
   // this.elements.body.empty();
   // this.makeFormInputs();
    
    // Generate image thingy
    if( this.params.loadedImages.length > 0 ) {
      var tmp = new Array();
      this.elements.previewImages = new Element('div', {
        'id' : 'compose-link-preview-images',
        'class' : 'compose-preview-images'
      }).inject(this.elements.body);

      this.params.assets.each(function(element, index) {
        if( !$type(this.params.loadedImages[index]) ) return;
        element.addClass('compose-preview-image-invisible').inject(this.elements.previewImages);
        if( !this.checkImageValid(element) ) {
          delete this.params.images[index];
          delete this.params.loadedImages[index];
          element.destroy();
        } else {
          element.removeClass('compose-preview-image-invisible').addClass('compose-preview-image-hidden');
          tmp.push(this.params.loadedImages[index]);
          element.erase('height');
          element.erase('width');
        }
      }.bind(this));

      this.params.loadedImages = tmp;

      if( this.params.loadedImages.length <= 0 ) {
        this.elements.previewImages.destroy();
      }
    }

    this.elements.previewInfo = new Element('div', {
      'id' : 'compose-link-preview-info',
      'class' : 'compose-preview-info'
    }).inject(this.elements.body);
    
    // Generate title and description
    this.elements.previewTitle = new Element('div', {
      'id' : 'compose-link-preview-title',
      'class' : 'compose-preview-title'
    }).inject(this.elements.previewInfo);

    this.elements.previewTitleLink = new Element('a', {
      'href' : this.params.uri,
      'html' : this.params.title
//       'events' : {
//         'click' : function(e) {
//           e.stop();
//           self.handleEditTitle(this);
//         }
//       }
    }).inject(this.elements.previewTitle);

    this.elements.previewDescription = new Element('div', {
      'id' : 'compose-link-preview-description',
      'class' : 'compose-preview-description',
      'html' : this.params.description
//       'events' : {
//         'click' : function(e) {
//           e.stop();
//           self.handleEditDescription(this);
//         }
//       }
    }).inject(this.elements.previewInfo);

    // Generate image selector thingy
    if( this.params.loadedImages.length > 0 ) {
      this.elements.previewOptions = new Element('div', {
        'id' : 'compose-link-preview-options',
        'class' : 'compose-preview-options'
      }).inject(this.elements.previewInfo);

      if( this.params.loadedImages.length > 1 ) {
        this.elements.previewChoose = new Element('div', {
          'id' : 'compose-link-preview-options-choose',
          'class' : 'compose-preview-options-choose',
          'html' : '<span>' + this._lang('Choose Image:') + '</span>'
        }).inject(this.elements.previewOptions);

        this.elements.previewPrevious = new Element('a', {
          'id' : 'compose-link-preview-options-previous',
          'class' : 'compose-preview-options-previous',
          'href' : 'javascript:void(0);',
          'html' : '&#171; ' + this._lang('Last'),
          'events' : {
            'click' : this.doSelectImagePrevious.bind(this)
          }
        }).inject(this.elements.previewChoose);

        this.elements.previewCount = new Element('span', {
          'id' : 'compose-link-preview-options-count',
          'class' : 'compose-preview-options-count'
        }).inject(this.elements.previewChoose);


        this.elements.previewPrevious = new Element('a', {
          'id' : 'compose-link-preview-options-next',
          'class' : 'compose-preview-options-next',
          'href' : 'javascript:void(0);',
          'html' : this._lang('Next') + ' &#187;',
          'events' : {
            'click' : this.doSelectImageNext.bind(this)
          }
        }).inject(this.elements.previewChoose);
      }

      this.elements.previewNoImage = new Element('div', {
        'id' : 'compose-link-preview-options-none',
        'class' : 'compose-preview-options-none'
      }).inject(this.elements.previewOptions);

//       this.elements.previewNoImageInput = new Element('input', {
//         'id' : 'compose-link-preview-options-none-input',
//         'class' : 'compose-preview-options-none-input',
//         'type' : 'checkbox',
//         'events' : {
//           'click' : this.doToggleNoImage.bind(this)
//         }
//       }).inject(this.elements.previewNoImage);
// 
//       this.elements.previewNoImageLabel = new Element('label', {
//         'for' : 'compose-link-preview-options-none-input',
//         'html' : this._lang('Don\'t show an image'),
//         'events' : {
//           //'click' : this.doToggleNoImage.bind(this)
//         }
//       }).inject(this.elements.previewNoImage);
      
			
			this.elements.NoImageInput = new Element('input', {
				'type' : 'hidden',
				'events' : {
					'click' : this.prefield()
				}
			}).inject(this.elements.previewNoImage);

      // Show first image
      this.setImageThumb(this.elements.previewImages.getChildren()[0]);
    }
    
    if(document.getElementById('seao_link_form_cancel')) {
			document.getElementById('seao_link_form_cancel').style.display = 'block';
		}
  },
	
	prefield : function () {
		document.getElementById('title').value = this.params.title;
		if(document.getElementById('description'))
			document.getElementById('description').value = this.params.description;
		if(document.getElementById('body'))
			document.getElementById('body').value = this.params.description;
		
		//document.getElementById('description').value = this.params.description;
		if(document.getElementById('seao_link_loading'))
			document.getElementById('seao_link_loading').style.display = 'none';
		//this.makeLoading('empty');
		//document.getElementById('photo').value = this.params.thumb;

		
	},
	
  checkImageValid : function(element) {
    var size = element.getSize();
    var sizeAlt = {x:element.get('width'),y:element.get('height')};
    var width = sizeAlt.x || size.x;
    var height = sizeAlt.y || size.y;
    var pixels = width * height;
    var aspect = width / height;
    
    // Debugging
    if( this.options.debug ) {
      console.log(element.get('src'), sizeAlt, size, width, height, pixels, aspect);
    }

    // Check aspect
    if( aspect > this.options.imageMaxAspect ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Aspect greater than max - ', element.get('src'), aspect, this.options.imageMaxAspect);
      }
      return false;
    } else if( aspect < this.options.imageMinAspect ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Aspect less than min - ', element.get('src'), aspect, this.options.imageMinAspect);
      }
      return false;
    }
    // Check min size
    if( width < this.options.imageMinSize ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Width less than min - ', element.get('src'), width, this.options.imageMinSize);
      }
      return false;
    } else if( height < this.options.imageMinSize ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Height less than min - ', element.get('src'), height, this.options.imageMinSize);
      }
      return false;
    }
    // Check max size
    if( width > this.options.imageMaxSize ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Width greater than max - ', element.get('src'), width, this.options.imageMaxSize);
      }
      return false;
    } else if( height > this.options.imageMaxSize ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Height greater than max - ', element.get('src'), height, this.options.imageMaxSize);
      }
      return false;
    }
    // Check  pixels
    if( pixels < this.options.imageMinPixels ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Pixel count less than min - ', element.get('src'), pixels, this.options.imageMinPixels);
      }
      return false;
    } else if( pixels > this.options.imageMaxPixels ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Pixel count greater than max - ', element.get('src'), pixels, this.options.imageMaxPixels);
      }
      return false;
    }

    return true;
  },

  doSelectImagePrevious : function() {
    if( this.elements.imageThumb && this.elements.imageThumb.getPrevious() ) {
      this.setImageThumb(this.elements.imageThumb.getPrevious());
    }
  },

  doSelectImageNext : function() {
    if( this.elements.imageThumb && this.elements.imageThumb.getNext() ) {
      this.setImageThumb(this.elements.imageThumb.getNext());
    }
  },

  doToggleNoImage : function() {
    if( !$type(this.params.thumb) ) {
      this.params.thumb = this.elements.imageThumb.src;
     // this.setFormInputValue('thumb', this.params.thumb);
      this.elements.previewImages.setStyle('display', '');
      if( this.elements.previewChoose ) this.elements.previewChoose.setStyle('display', '');
    } else {
      delete this.params.thumb;
     // this.setFormInputValue('thumb', '');
      this.elements.previewImages.setStyle('display', 'none');
      if( this.elements.previewChoose ) this.elements.previewChoose.setStyle('display', 'none');
    }
  },
	
	setFormInputValue : function(key, value) {
		if(document.getElementById('link_photo'))
				document.getElementById('link_photo').value = value;
	},
	
  setImageThumb : function(element) {
    // Hide old thumb
    if( this.elements.imageThumb ) {
      this.elements.imageThumb.addClass('compose-preview-image-hidden');
    }
    if( element ) {
      element.removeClass('compose-preview-image-hidden');
      this.elements.imageThumb = element;
      this.params.thumb = element.src;
      this.setFormInputValue('thumb', element.src);
      if( this.elements.previewCount ) {
        var index = this.params.loadedImages.indexOf(element.src);
        //this.elements.previewCount.set('html', ' | ' + (index + 1) + ' of ' + this.params.loadedImages.length + ' | ');
	if ( index < 0 ) { index = 0; }
        this.elements.previewCount.set('html', ' | ' + this._lang('%d of %d', index + 1, this.params.loadedImages.length) + ' | ');
      }
    } else {
      this.elements.imageThumb = false;
      delete this.params.thumb;
    }
  },

  makeFormInputs : function() {
   // this.ready();
    this.parent({
      'uri' : this.params.uri,
      'title' : this.params.title,
      'description' : this.params.description,
      'thumb' : this.params.thumb
    });
  },

  handleEditTitle : function(element) {
    element.setStyle('display', 'none');
    var input = new Element('input', {
      'type' : 'text',
      'value' : element.get('text').trim(),
      'events' : {
        'blur' : function() {
          if( input.value.trim() != '' ) {
            this.params.title = input.value;
            element.set('text', this.params.title);
           // this.setFormInputValue('title', this.params.title);
          }
          element.setStyle('display', '');
          input.destroy();
        }.bind(this)
      }
    }).inject(element, 'after');
    input.focus();
  },

  handleEditDescription : function(element) {
    element.setStyle('display', 'none');
    var input = new Element('textarea', {
      'html' : element.get('text').trim(),
      'events' : {
        'blur' : function() {
          if( input.value.trim() != '' ) {
            this.params.description = input.value;
            element.set('text', this.params.description);
            //this.setFormInputValue('description', this.params.description);
          }
          element.setStyle('display', '');
          input.destroy();
        }.bind(this)
      }
    }).inject(element, 'after');
    input.focus();
  },
	
	
	_lang : function() {
		try {
			if( arguments.length < 1 ) {
				return '';
			}
			
			var string = arguments[0];
			if( $type(this.options.lang) && $type(this.options.lang[string]) ) {
				string = this.options.lang[string];
			}
			
			if( arguments.length <= 1 ) {
				return string;
			}
			
			var args = new Array();
			for( var i = 1, l = arguments.length; i < l; i++ ) {
				args.push(arguments[i]);
			}
			
			return string.vsprintf(args);
		} catch( e ) {
			alert(e);
		}
	}

});



})(); // END NAMESPACE
