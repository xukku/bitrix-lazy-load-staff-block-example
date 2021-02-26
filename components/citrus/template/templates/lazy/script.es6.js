import * as BX from "main";
import {Reflection, type as Type, ajax as Ajax} from 'main.core';

const namespace = Reflection.namespace('Citrus.Template');

export class Component {
    constructor(params = {}) {
        if (!Type.isPlainObject(params)) {
            throw new Error('params should be an object');
        }
        if (!Type.isStringFilled(params.htmlId)) {
            throw new Error('params.htmlId should be a string');
        }
        if (!Type.isStringFilled(params.signedParameters)) {
            throw new Error('params.signedParameters should be a string');
        }

        this.signedParameters = params.signedParameters;
        this.container = document.getElementById(params.htmlId);
        this.lazyLoad = new BX.Citrus.Arealty.LazyLoad({
            elements_selector: `#${params.htmlId}`,
            unobserve_entered: true,
            callback_enter: () => this.loadComponent(),
        });
    }

    loadComponent() {
        return Ajax.runComponentAction('citrus:template', 'loadComponent', {
            mode: 'class',
            signedParameters: this.signedParameters,
            data: {}
        }).then((response) => BX.Runtime.html(this.container, response.data.html));
    }
}

namespace.Component = Component;
