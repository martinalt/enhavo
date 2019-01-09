define(["require", "exports", "app/Admin", "app/Router", "app/Form", "app/Form/Form", "app/Form/Form"], function (require, exports, admin, router, form, Form_1, Form_2) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    var DynamicFormConfig = /** @class */ (function () {
        function DynamicFormConfig() {
        }
        return DynamicFormConfig;
    }());
    exports.DynamicFormConfig = DynamicFormConfig;
    var DynamicForm = /** @class */ (function () {
        function DynamicForm(element, config, scope) {
            if (config === void 0) { config = null; }
            if (scope === void 0) { scope = null; }
            this.items = [];
            this.placeholderIndex = -1;
            this.collapse = true;
            this.$element = $(element);
            this.$container = this.$element.children('[data-dynamic-form-container]');
            this.scope = scope;
            this.initMenu();
            this.initActions();
            this.initItems();
            this.initContainer();
            if (config == null) {
                this.config = this.$element.data('dynamic-config');
            }
            else {
                this.config = config;
            }
            this.formListener = new Form_1.FormListener();
            var self = this;
            this.formListener.onConvert(function (event) {
                var html = event.getHtml();
                html = html.replace(new RegExp(self.config.prototypeName, 'g'), String(self.placeholderIndex));
                event.setHtml(html);
            });
            this.placeholderIndex = this.$container.children('[data-dynamic-form-item]').length - 1;
        }
        DynamicForm.apply = function (element) {
            $(element).find('[data-dynamic-form]').each(function () {
                new DynamicForm(this);
            });
        };
        DynamicForm.prototype.initItems = function () {
            var items = [];
            var dynamicForm = this;
            this.$container.children('[data-dynamic-form-item]').each(function () {
                items.push(new DynamicFormItem(this, dynamicForm));
            });
            this.items = items;
        };
        DynamicForm.prototype.initContainer = function () {
            var dynamicForm = this;
            if (typeof this.$container.attr('data-reindexable') != 'undefined') {
                // Save initial index
                this.$container.data('initial-list-index', this.$container.children('[data-dynamic-form-item]').length);
            }
            this.setOrder();
            this.$container.children('[data-dynamic-form-add-button]').each(function () {
                new DynamicFormItemAddButton(this, dynamicForm);
            });
        };
        DynamicForm.prototype.initActions = function () {
            var dynamicForm = this;
            if (this.collapse) {
                dynamicForm.collapseAll();
            }
            else {
                dynamicForm.expandAll();
            }
            this.$element.children('[data-dynamic-form-action]').children('[data-dynamic-form-action-collapse-all]').click(function () {
                dynamicForm.collapseAll();
            });
            this.$element.children('[data-dynamic-form-action]').children('[data-dynamic-form-action-expand-all]').click(function () {
                dynamicForm.expandAll();
            });
        };
        DynamicForm.prototype.getConfig = function () {
            return this.config;
        };
        DynamicForm.prototype.getScope = function () {
            return this.scope;
        };
        DynamicForm.prototype.collapseAll = function () {
            this.$element.children('[data-dynamic-form-action]').children('[data-dynamic-form-action-collapse-all]').hide();
            this.$element.children('[data-dynamic-form-action]').children('[data-dynamic-form-action-expand-all]').show();
            this.collapse = true;
            for (var _i = 0, _a = this.items; _i < _a.length; _i++) {
                var item = _a[_i];
                item.collapse();
            }
        };
        DynamicForm.prototype.expandAll = function () {
            this.$element.children('[data-dynamic-form-action]').children('[data-dynamic-form-action-collapse-all]').show();
            this.$element.children('[data-dynamic-form-action]').children('[data-dynamic-form-action-expand-all]').hide();
            this.collapse = false;
            for (var _i = 0, _a = this.items; _i < _a.length; _i++) {
                var item = _a[_i];
                item.expand();
            }
        };
        DynamicForm.prototype.initMenu = function () {
            var element = this.$element.children('[data-dynamic-form-menu]').get(0);
            this.menu = new DynamicFormMenu(element, this);
        };
        DynamicForm.prototype.getMenu = function () {
            return this.menu;
        };
        DynamicForm.prototype.isCollapse = function () {
            return this.collapse;
        };
        DynamicForm.prototype.addItem = function (type, button) {
            var url = router.generate(this.config.route, {
                type: type
            });
            var formName = this.$element.data('dynamic-form-name') + '[' + this.config.prototypeName + ']';
            this.placeholderIndex++;
            var dynamicForm = this;
            this.startLoading();
            $.ajax({
                type: 'POST',
                data: {
                    formName: formName,
                    prototypeName: this.config.prototypeName
                },
                url: url,
                success: function (data) {
                    dynamicForm.endLoading();
                    var form = new Form_2.FormInitializer();
                    form.setHtml(data);
                    form.insertAfter(button.getElement());
                    dynamicForm.items.push(new DynamicFormItem(form.getElement(), dynamicForm));
                    var newButton = dynamicForm.createAddButton();
                    $(form.getElement()).after(newButton.getElement());
                    dynamicForm.setOrder();
                },
                error: function () {
                    dynamicForm.endLoading();
                }
            });
        };
        DynamicForm.prototype.createAddButton = function () {
            var html = this.$element.data('dynamic-form-add-button-template').trim();
            var element = $.parseHTML(html)[0];
            return new DynamicFormItemAddButton(element, this);
        };
        DynamicForm.prototype.setOrder = function () {
            this.$container.find('[data-position]').each(function (index, element) {
                $(element).val(index + 1);
            });
        };
        ;
        DynamicForm.prototype.startLoading = function () {
            admin.openLoadingOverlay();
        };
        DynamicForm.prototype.endLoading = function () {
            admin.closeLoadingOverlay();
        };
        DynamicForm.prototype.moveItemUp = function (item, callback) {
            if (callback === void 0) { callback = function () { }; }
            var index = this.$container.children('[data-dynamic-form-item]').index(item.getElement());
            var self = this;
            if (index > 0) {
                var buttonToMove_1 = $(this.$container.children('[data-dynamic-form-add-button]').get(index + 1));
                var buttonTarget_1 = $(this.$container.children('[data-dynamic-form-add-button]').get(index - 1));
                var domElementToMove_1 = $(item.getElement());
                domElementToMove_1.slideUp(200, function () {
                    buttonTarget_1.after(domElementToMove_1);
                    domElementToMove_1.after(buttonToMove_1);
                    domElementToMove_1.slideDown(200, function () {
                        self.setOrder();
                        if (typeof callback != "undefined") {
                            callback();
                        }
                    });
                });
            }
            else {
                this.setOrder();
                if (typeof callback != "undefined") {
                    callback();
                }
            }
        };
        DynamicForm.prototype.moveItemDown = function (item, callback) {
            if (callback === void 0) { callback = function () { }; }
            var index = this.$container.children('[data-dynamic-form-item]').index(item.getElement());
            var size = this.$container.children('[data-dynamic-form-item]').length;
            var self = this;
            if (index < (size - 1)) {
                var buttonToMove_2 = $(this.$container.children('[data-dynamic-form-add-button]').get(index + 1));
                var buttonTarget_2 = $(this.$container.children('[data-dynamic-form-add-button]').get(index + 2));
                var domElementToMove_2 = $(item.getElement());
                domElementToMove_2.slideUp(200, function () {
                    buttonTarget_2.after(domElementToMove_2);
                    domElementToMove_2.after(buttonToMove_2);
                    domElementToMove_2.slideDown(200, function () {
                        self.setOrder();
                        if (typeof callback != "undefined") {
                            callback();
                        }
                    });
                });
            }
            else {
                this.setOrder();
                if (typeof callback != "undefined") {
                    callback();
                }
            }
        };
        DynamicForm.prototype.removeItem = function (item) {
            $(item.getElement()).next().remove();
            $(item.getElement()).css({ opacity: 0, transition: 'opacity 550ms' }).slideUp(350, function () {
                this.remove();
            });
            var index = this.items.indexOf(item);
            if (index > -1) {
                this.items.splice(index, 1);
            }
        };
        return DynamicForm;
    }());
    exports.DynamicForm = DynamicForm;
    var DynamicFormMenu = /** @class */ (function () {
        function DynamicFormMenu(element, dynamicForm) {
            this.dynamicForm = dynamicForm;
            this.$element = $(element);
            this.initActions();
        }
        DynamicFormMenu.prototype.initActions = function () {
            var menu = this;
            this.$element.find('[data-dynamic-form-menu-item]').click(function () {
                var name = $(this).data('dynamic-form-menu-item');
                menu.dynamicForm.addItem(name, menu.button);
                menu.hide();
            });
        };
        DynamicFormMenu.prototype.show = function (button) {
            if (this.button === button) {
                this.hide();
                return;
            }
            this.button = button;
            var position = $(button.getElement()).position();
            var dropDown = true;
            var scope;
            scope = this.dynamicForm.getScope();
            if (scope == null) {
                scope = $('body').get(0);
            }
            var topOffset = this.topToElement(button.getElement(), scope, 0);
            var center = $(button.getElement()).height() / 2 + topOffset;
            var halfHeight = $(scope).height() / 2;
            if (halfHeight < center) {
                dropDown = false;
            }
            if (dropDown) {
                this.$element.addClass('topTriangle');
                this.$element.css('top', 35 + position.top + 'px');
            }
            else {
                this.$element.addClass('bottomTriangle');
                this.$element.css('top', -1 * this.$element.height() - 25 + position.top + 'px');
            }
            this.$element.css('left', position.left + 'px');
            this.$element.show();
        };
        DynamicFormMenu.prototype.topToElement = function (element, toElement, top) {
            if (top === void 0) { top = 0; }
            var parent = $(element).offsetParent().get(0);
            if (parent == $('html').get(0)) {
                return top;
            }
            var topOffset = $(element).position().top;
            if (toElement == parent) {
                return top + topOffset;
            }
            return this.topToElement(parent, toElement, top + topOffset);
        };
        DynamicFormMenu.prototype.hide = function () {
            this.button = null;
            this.$element.hide();
        };
        return DynamicFormMenu;
    }());
    exports.DynamicFormMenu = DynamicFormMenu;
    var DynamicFormItem = /** @class */ (function () {
        function DynamicFormItem(element, dynamicForm) {
            this.dynamicForm = dynamicForm;
            this.$element = $(element);
            this.initActions();
            if (dynamicForm.isCollapse()) {
                this.collapse();
            }
            else {
                this.expand();
            }
        }
        DynamicFormItem.prototype.initActions = function () {
            var dynamicForm = this;
            var $actions = this.$element.children('[data-dynamic-form-item-action]');
            $actions.find('[data-dynamic-form-item-action-up]').click(function () {
                dynamicForm.up();
            });
            $actions.find('[data-dynamic-form-item-action-down]').click(function () {
                dynamicForm.down();
            });
            $actions.find('[data-dynamic-form-item-action-remove]').click(function () {
                dynamicForm.remove();
            });
            $actions.find('[data-dynamic-form-item-action-collapse]').click(function () {
                dynamicForm.collapse();
            });
            $actions.find('[data-dynamic-form-item-action-expand]').click(function () {
                dynamicForm.expand();
            });
        };
        DynamicFormItem.prototype.getElement = function () {
            return this.$element.get(0);
        };
        DynamicFormItem.prototype.collapse = function () {
            var $actions = this.$element.children('[data-dynamic-form-item-action]');
            $actions.find('[data-dynamic-form-item-action-expand]').show();
            $actions.find('[data-dynamic-form-item-action-collapse]').hide();
            this.$element.children('[data-dynamic-form-item-container]').hide();
        };
        DynamicFormItem.prototype.expand = function () {
            var $actions = this.$element.children('[data-dynamic-form-item-action]');
            $actions.find('[data-dynamic-form-item-action-expand]').hide();
            $actions.find('[data-dynamic-form-item-action-collapse]').show();
            this.$element.children('[data-dynamic-form-item-container]').show();
        };
        DynamicFormItem.prototype.up = function () {
            var wyswigs = this.$element.find('[data-wysiwyg]');
            var self = this;
            if (wyswigs.length) {
                form.destroyWysiwyg(this.$element);
                this.dynamicForm.moveItemUp(this, function () {
                    form.initWysiwyg(self.getElement());
                });
            }
            else {
                this.dynamicForm.moveItemUp(this);
            }
        };
        DynamicFormItem.prototype.down = function () {
            var wyswigs = this.$element.find('[data-wysiwyg]');
            var self = this;
            if (wyswigs.length) {
                form.destroyWysiwyg(this.$element);
                this.dynamicForm.moveItemDown(this, function () {
                    form.initWysiwyg(self.getElement());
                });
            }
            else {
                this.dynamicForm.moveItemDown(this);
            }
        };
        DynamicFormItem.prototype.remove = function () {
            this.dynamicForm.removeItem(this);
        };
        return DynamicFormItem;
    }());
    exports.DynamicFormItem = DynamicFormItem;
    var DynamicFormItemAddButton = /** @class */ (function () {
        function DynamicFormItemAddButton(element, dynamicForm) {
            this.dynamicForm = dynamicForm;
            this.$element = $(element);
            var that = this;
            this.$element.click(function () {
                dynamicForm.getMenu().show(that);
            });
        }
        DynamicFormItemAddButton.prototype.getElement = function () {
            return this.$element.get(0);
        };
        return DynamicFormItemAddButton;
    }());
    exports.DynamicFormItemAddButton = DynamicFormItemAddButton;
});
