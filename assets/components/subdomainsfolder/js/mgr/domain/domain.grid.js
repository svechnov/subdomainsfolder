subdomainsfolder.grid.Domain = function (config) {
    config = config || {};

	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: false,
		tpl: new Ext.Template('<p class="desc">{description}</p>'),
		renderer: function(v, p, record) {
			return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';
		}
	});

    this.dd = function (grid) {
        this.dropTarget = new Ext.dd.DropTarget(grid.container, {
            ddGroup: 'dd',
            copy: false,
            notifyDrop: function (dd, e, data) {
                var store = grid.store.data.items;
                var target = store[dd.getDragData(e).rowIndex].id;
                var source = store[data.rowIndex].id;
                if (target != source) {
                    dd.el.mask(_('loading'), 'x-mask-loading');
                    MODx.Ajax.request({
                        url: subdomainsfolder.config.connector_url,
                        params: {
                            action: config.action || 'mgr/domain/sort',
                            source: source,
                            target: target
                        },
                        listeners: {
                            success: {
                                fn: function (r) {
                                    dd.el.unmask();
                                    grid.refresh();
                                },
                                scope: grid
                            },
                            failure: {
                                fn: function (r) {
                                    dd.el.unmask();
                                },
                                scope: grid
                            }
                        }
                    });
                }
            }
        });
    };

    this.sm = new Ext.grid.CheckboxSelectionModel();

    Ext.applyIf(config, {
        id: 'subdomainsfolder-grid-domain',
        url: subdomainsfolder.config.connector_url,
        baseParams: {
            action: 'mgr/domain/getlist'
        },
        save_action: 'mgr/domain/updatefromgrid',
        autosave: true,
        save_callback: this._updateRow,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        listeners: this.getListeners(config),

        sm: this.sm,
        plugins: this.exp,
        /*ddGroup: 'dd',
         enableDragDrop: true,*/

        autoHeight: true,
        paging: true,
        pageSize: 10,
        remoteSort: true,
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0
        },
        cls: 'subdomainsfolder-grid',
        bodyCssClass: 'grid-with-buttons',
        stateful: true,
        stateId: 'subdomainsfolder-grid-domain-state'

    });
    subdomainsfolder.grid.Domain.superclass.constructor.call(this, config);

};
Ext.extend(subdomainsfolder.grid.Domain, MODx.grid.Grid, {
    windows: {},

    getFields: function (config) {
        var fields = subdomainsfolder.config.fields_grid_domain;

        return fields;
    },

    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="icon icon-cogs"></i> ', // + _('subdomainsfolder_actions'),
            menu: [{
                text: '<i class="icon icon-plus"></i> ' + _('subdomainsfolder_action_create'),
                cls: 'subdomainsfolder-cogs',
                handler: this.create,
                scope: this
            }, {
                text: '<i class="icon icon-trash-o red"></i> ' + _('subdomainsfolder_action_remove'),
                cls: 'subdomainsfolder-cogs',
                handler: this.remove,
                scope: this
            }, '-', {
                text: '<i class="icon icon-toggle-on green"></i> ' + _('subdomainsfolder_action_active'),
                cls: 'subdomainsfolder-cogs',
                handler: this.active,
                scope: this
            }, {
                text: '<i class="icon icon-toggle-off red"></i> ' + _('subdomainsfolder_action_inactive'),
                cls: 'subdomainsfolder-cogs',
                handler: this.inactive,
                scope: this
            }]
        });

        tbar.push('->');

        if (1 != MODx.config.subdomainsfolder_field_search_domain_disable) {
            tbar.push({
                xtype: 'subdomainsfolder-field-search',
                width: 210,
                listeners: {
                    search: {
                        fn: function (field) {
                            this._doSearch(field);
                        },
                        scope: this
                    },
                    clear: {
                        fn: function (field) {
                            field.setValue('');
                            this._clearSearch();
                        },
                        scope: this
                    }
                }
            });
        }

        return tbar;
    },

    getColumns: function (config) {
        var columns = [this.exp, this.sm];
        var add = {
            id: {
                width: 10,
                sortable: true
            },
			domain: {
				width: 25,
				sortable: true
			},
			resource: {
				width: 20,
				sortable: true
			},
			context: {
				width: 25,
				sortable: true
			},
            actions: {
                width: 25,
                sortable: false,
                renderer: subdomainsfolder.tools.renderActions,
                id: 'actions'
            }
        };

        for (var i = 0; i < subdomainsfolder.config.fields_grid_domain.length; i++) {
            var field = subdomainsfolder.config.fields_grid_domain[i];
            if (add[field]) {
                Ext.applyIf(add[field], {
                    header: _('subdomainsfolder_header_' + field),
                    tooltip: _('subdomainsfolder_tooltip_' + field),
                    dataIndex: field
                });
                columns.push(add[field]);
            }
        }

		console.log(columns);

        return columns;
    },

    getListeners: function (config) {
        return {
            render: {
                fn: this.dd,
                scope: this
            }
        };
    },

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);
        var menu = subdomainsfolder.tools.getMenu(row.data['actions'], this, ids);
        this.addContextMenuItem(menu);
    },


    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                } else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },


    setAction: function (method, field, value) {
        var ids = this._getSelectedIds();
        if (!ids.length && (field !== 'false')) {
            return false;
        }
        MODx.Ajax.request({
            url: subdomainsfolder.config.connector_url,
            params: {
                action: 'mgr/domain/multiple',
                method: method,
                field_name: field,
                field_value: value,
                ids: Ext.util.JSON.encode(ids)
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    },
                    scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(_('error'), response.message);
                    },
                    scope: this
                }
            }
        })
    },

	active: function(btn, e) {
		this.setAction('setproperty', 'active', 1);
	},

	inactive: function(btn, e) {
		this.setAction('setproperty', 'active', 0);
	},

    remove: function () {
        Ext.MessageBox.confirm(
            _('subdomainsfolder_action_remove'),
            _('subdomainsfolder_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.setAction('remove');
                }
            },
            this
        );
    },

    create: function (btn, e) {
        var record = {
            context: 'web',
            active: 1
        };
        var w = MODx.load({
            xtype: 'subdomainsfolder-window-create-domain',
			record: record,
            class: this.config.class,
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues(record);
        w.show(e.target);
    },

    update: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/domain/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var record = r.object;
                        var w = MODx.load({
                            xtype: 'subdomainsfolder-window-create-domain',
                            title: _('subdomainsfolder_action_update'),
                            action: 'mgr/domain/update',
                            class: this.config.class,
                            record: record,
                            update: true,
                            listeners: {
                                success: {
                                    fn: this.refresh,
                                    scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(record);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },

    _filterByCombo: function (cb) {
        this.getStore().baseParams[cb.name] = cb.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _doSearch: function (cb) {
        this.getStore().baseParams.query = cb.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function () {
        this.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },

    _updateRow: function (response) {
        this.refresh();
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    }

});
Ext.reg('subdomainsfolder-grid-domain', subdomainsfolder.grid.Domain);
