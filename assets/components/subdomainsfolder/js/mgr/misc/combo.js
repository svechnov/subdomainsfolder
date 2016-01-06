Ext.namespace('subdomainsfolder.combo');

subdomainsfolder.combo.Search = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		xtype: 'twintrigger',
		ctCls: 'x-field-search',
		allowBlank: true,
		msgTarget: 'under',
		emptyText: _('search'),
		name: 'query',
		triggerAction: 'all',
		clearBtnCls: 'x-field-subdomainsfolder-search-clear',
		searchBtnCls: 'x-field-subdomainsfolder-search-go',
		onTrigger1Click: this._triggerSearch,
		onTrigger2Click: this._triggerClear
	});
	subdomainsfolder.combo.Search.superclass.constructor.call(this, config);
	this.on('render', function () {
		this.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
			this._triggerSearch();
		}, this);
	});
	this.addEvents('clear', 'search');
};
Ext.extend(subdomainsfolder.combo.Search, Ext.form.TwinTriggerField, {

	initComponent: function () {
		Ext.form.TwinTriggerField.superclass.initComponent.call(this);
		this.triggerConfig = {
			tag: 'span',
			cls: 'x-field-search-btns',
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger ' + this.searchBtnCls
			}, {
				tag: 'div',
				cls: 'x-form-trigger ' + this.clearBtnCls
			}]
		};
	},

	_triggerSearch: function () {
		this.fireEvent('search', this);
	},

	_triggerClear: function () {
		this.fireEvent('clear', this);
	}

});
Ext.reg('subdomainsfolder-field-search', subdomainsfolder.combo.Search);


subdomainsfolder.combo.Context = function (config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-subdomainsfolder-context-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-subdomainsfolder-context-clear'
			});
		}

		config.initTrigger = function () {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function (t, all, index) {
				t.hide = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'context',
		hiddenName: config.name || 'context',
		displayField: 'name',
		valueField: 'key',
		editable: true,
		fields: ['name', 'key'],
		pageSize: 10,
		emptyText: _('subdomainsfolder_combo_select'),
		hideMode: 'offsets',
		url: MODx.config.connector_url,
		baseParams: {
			action: 'context/getlist',
			combo: true
		},
		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item">',
			'<small>({key})</small> <b>{name}</b></span>',
			'</div></tpl>',
			{
				compiled: true
			}),
		cls: 'input-combo-context',
		clearValue: function () {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
		},

		getTrigger: function (index) {
			return this.triggers[index];
		},

		onTrigger1Click: function () {
			this.onTriggerClick();
		},

		onTrigger2Click: function () {
			this.clearValue();
		}
	});
	subdomainsfolder.combo.Context.superclass.constructor.call(this, config);

};
Ext.extend(subdomainsfolder.combo.Context, MODx.combo.ComboBox);
Ext.reg('subdomainsfolder-combo-context', subdomainsfolder.combo.Context);


subdomainsfolder.combo.Resource = function (config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-subdomainsfolder-resource-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-subdomainsfolder-resource-clear'
			});
		}

		config.initTrigger = function () {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function (t, all, index) {
				t.hide = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'resource',
		hiddenName: config.name || 'resource',
		displayField: 'pagetitle',
		valueField: 'id',
		editable: true,
		fields: ['pagetitle', 'id'],
		pageSize: 10,
		emptyText: _('subdomainsfolder_combo_select'),
		hideMode: 'offsets',
		url: subdomainsfolder.config.connector_url,
		baseParams: {
			action: 'mgr/misc/resource/getlist',
			client_status: 1,
			combo: true
		},
		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item">',
			'<small>({id})</small> <b>{pagetitle}</b>',
			'</div></tpl>',
			{
				compiled: true
			}),
		cls: 'input-combo-subdomainsfolder-resource',
		clearValue: function () {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
		},

		getTrigger: function (index) {
			return this.triggers[index];
		},

		onTrigger1Click: function () {
			this.onTriggerClick();
		},

		onTrigger2Click: function () {
			this.clearValue();
		}
	});
	subdomainsfolder.combo.Resource.superclass.constructor.call(this, config);

};
Ext.extend(subdomainsfolder.combo.Resource, MODx.combo.ComboBox);
Ext.reg('subdomainsfolder-combo-resource', subdomainsfolder.combo.Resource);

