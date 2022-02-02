import { Component,Input } from '@angular/core';

/**
 * Generated class for the BreadcrumbComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
	selector: 'breadcrumb',
	templateUrl: 'breadcrumb.html',
})
export class BreadcrumbComponent {
	@Input('data') data: any = [];

	constructor() {
		
	}
}
