subdomainsfolder.panel.Domain = function(config) {
	if (!config.class) {
		config.class = 'sbdfDomain';
	}
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		cls: 'subdomainsfolder-formpanel',
		layout: 'anchor',
		hideMode: 'offset',
		items: [{
			xtype: 'modx-tabs',
			defaults: {
				border: false,
				autoHeight: true
			},
			border: true,
			hideMode: 'offset',
			items: [{
				title: _('subdomainsfolder_domains'),
				layout: 'anchor',
				items: [{
					html: _('subdomainsfolder_domains_intro'),
					cls: 'panel-desc'
				}, {
					xtype: 'subdomainsfolder-grid-domain',
					class: config.class,
					cls: 'main-wrapper'
				}]
			}]
		}]
	});
	subdomainsfolder.panel.Domain.superclass.constructor.call(this, config);
};
Ext.extend(subdomainsfolder.panel.Domain, MODx.Panel);
Ext.reg('subdomainsfolder-panel-domain', subdomainsfolder.panel.Domain);
