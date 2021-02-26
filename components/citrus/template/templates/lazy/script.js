(function (exports,BX,main_core) {
    'use strict';

    var namespace = main_core.Reflection.namespace('Citrus.Template');
    var Component = /*#__PURE__*/function () {
      function Component() {
        var _this = this;

        var params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
        babelHelpers.classCallCheck(this, Component);

        if (!main_core.type.isPlainObject(params)) {
          throw new Error('params should be an object');
        }

        if (!main_core.type.isStringFilled(params.htmlId)) {
          throw new Error('params.htmlId should be a string');
        }

        if (!main_core.type.isStringFilled(params.signedParameters)) {
          throw new Error('params.signedParameters should be a string');
        }

        this.signedParameters = params.signedParameters;
        this.container = document.getElementById(params.htmlId);
        this.lazyLoad = new BX.Citrus.Arealty.LazyLoad({
          elements_selector: "#".concat(params.htmlId),
          unobserve_entered: true,
          callback_enter: function callback_enter() {
            return _this.loadComponent();
          }
        });
      }

      babelHelpers.createClass(Component, [{
        key: "loadComponent",
        value: function loadComponent() {
          var _this2 = this;

          return main_core.ajax.runComponentAction('citrus:template', 'loadComponent', {
            mode: 'class',
            signedParameters: this.signedParameters,
            data: {}
          }).then(function (response) {
            return BX.Runtime.html(_this2.container, response.data.html);
          });
        }
      }]);
      return Component;
    }();
    namespace.Component = Component;

    exports.Component = Component;

}((this.window = this.window || {}),BX,BX));
//# sourceMappingURL=script.js.map
