import { Component } from '@angular/core';
import { NavParams, ViewController } from 'ionic-angular';

/**
 * Generated class for the ModalComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
	selector: 'modal',
	templateUrl: 'modal.html',
})
export class ModalComponent {
	body: string;
	bgColor: string = '#DC78AE !important';
	timerbgColor: string = '#604A70 !important';
	height: string = 'auto';
	title: string;
	constructor(public viewCtrl: ViewController, params: NavParams) {
		this.body = params.get('body').replace(/(?:\r\n|\r|\n)/g, '<br />');
		if (params.get('bgColor')) {
			this.bgColor = params.get('bgColor');
			
		}
		if (params.get('type') && params.get('type') == 'topic') {
			this.height = '100%';
			
		}

		if (params.get('type') && params.get('type') == 'meditation_timer') {
			this.height = '100%';			
		}

		this.title = params.get('title');
	}

	dismiss() {
		this.viewCtrl.dismiss();
	}
}
