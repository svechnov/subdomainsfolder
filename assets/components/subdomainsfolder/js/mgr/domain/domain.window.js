subdomainsfolder.window.CreateDomain = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		title: _('create'),
		width: 550,
		autoHeight: true,
		url: subdomainsfolder.config.connector_url,
		action: 'mgr/status/create',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	subdomainsfolder.window.CreateDomain.superclass.constructor.call(this, config);
};
Ext.extend(subdomainsfolder.window.CreateDomain, MODx.Window, {

	getFields: function (config) {
		return [{
			xtype: 'hidden',
			name: 'id'
		}, {
			xtype: 'textfield',
			fieldLabel: _('subdomainsfolder_domain'),
			name: 'domain',
			anchor: '99%',
			allowBlank: false
		}, /*{
			items: [{
				layout: 'form',
				cls: 'modx-panel',
				items: [{
					layout: 'column',
					border: false,
					items: [{
						columnWidth: .49,
						border: false,
						layout: 'form',
						items: this.getLeftFields(config)
					}, {
						columnWidth: .505,
						border: false,
						layout: 'form',
						cls: 'right-column',
						items: this.getRightFields(config)
					}]
				}]
			}]
		},*/ {
			xtype: 'subdomainsfolder-combo-resource',
			custm: true,
			clear: true,
			fieldLabel: _('subdomainsfolder_resource'),
			name: 'resource',
			anchor: '99%',
			allowBlank: false
		}, {
			xtype: 'checkboxgroup',
			hideLabel: true,
			columns: 3,
			items: [{
				xtype: 'xcheckbox',
				boxLabel: _('subdomainsfolder_active'),
				name: 'active',
				checked: config.record.active
			}]
		}];
	},

	getLeftFields: function (config) {
		return [{
			xtype: 'subdomainsfolder-combo-resource',
			custm: true,
			clear: true,
			fieldLabel: _('subdomainsfolder_resource'),
			name: 'resource',
			anchor: '99%',
			allowBlank: false
		}];
	},

	getRightFields: function (config) {
		return [{
			xtype: 'subdomainsfolder-combo-context',
			custm: true,
			clear: true,
			fieldLabel: _('subdomainsfolder_context'),
			name: 'context',
			anchor: '99%',
			allowBlank: false
		}];
	}

});
Ext.reg('subdomainsfolder-window-create-domain', subdomainsfolder.window.CreateDomain);
