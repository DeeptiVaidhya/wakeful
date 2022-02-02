import { Component } from '@angular/core';
import { CONSTANTS } from '../../config/constants';
import { NavController, Events } from 'ionic-angular';
import { TabsPage } from '../../pages/tabs/tabs';
import { HomePage } from '../../pages/home/home';
import { Helper } from '../../providers/helper';
import { Storage } from '@ionic/storage';
/**
 * Generated class for the BackStaticHeaderComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
	selector: 'back-static-header',
	templateUrl: 'back-static-header.html',
})
export class BackStaticHeaderComponent {
	APPTITLE: string;
	notificationCount = 0;
	constructor(private navCtrl: NavController,private helper:Helper, public storage: Storage, public events:Events) {
		this.APPTITLE = CONSTANTS.APP_TITLE;
		this.events.subscribe('user:notification', () => {
			// notification count subscribe
			this.storage.get('notification_count').then(notificationCount => {
				this.notificationCount = notificationCount ;
			});
		});
	}

	goBack() {
		this.helper.authenticated(false).then(
			response => {
				this.navCtrl.setRoot(TabsPage, { selectIndex: 0 });
			},
			err => {
				this.navCtrl.setRoot(HomePage);
			}
		);
		
	}
}
