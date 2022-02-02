import { Component } from '@angular/core';
import { AboutPage } from '../../pages/about/about';
import { ContactPage } from '../../pages/contact/contact';
import { OverviewPage } from '../../pages/overview/overview';
import { NavController } from 'ionic-angular';
/**
 * Generated class for the StaticFooterComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
	selector: 'static-footer',
	templateUrl: 'static-footer.html',
})
export class StaticFooterComponent {
	text: string;

	constructor(private navCtrl: NavController) {
	}

	overview() {
		this.navCtrl.setRoot(OverviewPage);
	}

	about() {
		this.navCtrl.setRoot(AboutPage);
	}

	contact() {
		this.navCtrl.setRoot(ContactPage);
	}

}
