import { Component, Input } from '@angular/core';
import { NavParams , Events } from 'ionic-angular';
import { Storage } from '@ionic/storage';
// import { Title } from '@angular/platform-browser/src/browser/title';

/**
 * Generated class for the AppHeaderComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
	selector: 'app-header',
	templateUrl: 'app-header.html',
})
export class AppHeaderComponent {
	@Input() title;
	@Input() isShownImage=true;
	notificationCount = 0;
	constructor(params: NavParams, public storage: Storage, public events: Events) {
		this.events.subscribe('user:notification', () => {
			// notification count subscribe
			this.storage.get('notification_count').then(notificationCount => {
				this.notificationCount = notificationCount ;
			});
		});
		
	}
	

	/*@Input()
	set title(name: string) {
		// Here you can do what you want with the variable
		this._title = (name && name.trim()) || '<no name set>';
	}

	get title() {
		return this._title;
	}*/
}
