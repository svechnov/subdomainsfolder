var subdomainsfolder = function (config) {
	config = config || {};
	subdomainsfolder.superclass.constructor.call(this, config);
};
Ext.extend(subdomainsfolder, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, tools: {}
});
Ext.reg('subdomainsfolder', subdomainsfolder);

subdomainsfolder = new subdomainsfolder();