/**
 * SEAOAutocompleter.Request
 *
 * http://digitarald.de/project/autocompleter/
 *
 * @version		1.1.2
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */

SEAOAutocompleter.Request = new Class({

	Extends: SEAOAutocompleter,

	options: {/*
		indicator: null,
		indicatorClass: null,
		onRequest: $empty,
		onComplete: $empty,*/
		postData: {},
		ajaxOptions: {},
		postVar: 'value',
                delay: 250

	},

	query: function(){
		var data = $unlink(this.options.postData) || {};
		data[this.options.postVar] = this.queryValue;
		var indicator = $(this.options.indicator);
		if (indicator) indicator.setStyle('display', '');
		var cls = this.options.indicatorClass;
		if (cls) this.element.addClass(cls);
		this.fireEvent('onRequest', [this.element, this.request, data, this.queryValue]);
		this.request.send({'data': data});
	},

	/**
	 * queryResponse - abstract
	 *
	 * Inherated classes have to extend this function and use this.parent()
	 */
	queryResponse: function() {
		var indicator = $(this.options.indicator);
		if (indicator) indicator.setStyle('display', 'none');
		var cls = this.options.indicatorClass;
		if (cls) this.element.removeClass(cls);
		return this.fireEvent('onComplete', [this.element, this.request]);
	}

});

SEAOAutocompleter.Request.JSON = new Class({

	Extends: SEAOAutocompleter.Request,

	initialize: function(el, url, options) {
		this.parent(el, options);
		this.request = new Request.JSON($merge({
			'url': url,
			'link': 'cancel'
		}, this.options.ajaxOptions)).addEvent('onComplete', this.queryResponse.bind(this));
	},

	queryResponse: function(response) {
		this.parent();
		this.update(response);
	}

});

SEAOAutocompleter.Request.HTML = new Class({

	Extends: SEAOAutocompleter.Request,

	initialize: function(el, url, options) {
		this.parent(el, options);
		this.request = new Request.HTML($merge({
			'url': url,
			'link': 'cancel',
			'update': this.choices
		}, this.options.ajaxOptions)).addEvent('onComplete', this.queryResponse.bind(this));
	},

	queryResponse: function(tree, elements) {
		this.parent();
		if (!elements || !elements.length) {
			this.hideChoices();
		} else {
			this.choices.getChildren(this.options.choicesMatch).each(this.options.injectChoice || function(choice) {
				var value = choice.innerHTML;
				choice.inputValue = value;
				this.addChoiceEvents(choice.set('html', this.markQueryValue(value)));
			}, this);
			this.showChoices();
		}

	}

});

/* compatibility */

SEAOAutocompleter.Ajax = {
	Base: SEAOAutocompleter.Request,
	Json: SEAOAutocompleter.Request.JSON,
	Xhtml: SEAOAutocompleter.Request.HTML
};
