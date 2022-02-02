import { Component } from '@angular/core';
import { NavController,IonicPage } from 'ionic-angular';
import { Helper } from '../../providers/helper';
import { VgAPI } from 'videogular2/core';

@IonicPage()
@Component({
	selector: 'page-about',
	templateUrl: 'about.html',
})
export class AboutPage {
	text: string;
	api: VgAPI;
	players = [];
	user:boolean = false;
	administartor:boolean = false;

	constructor(public navCtrl: NavController, public helper: Helper) {	}

	onPlayerReady(api: VgAPI, index) {
		if (!this.players[index]) {
			this.players[index] = {};
		}
		this.players[index].api = api;
		this.players[index].api.getDefaultMedia().subscriptions.ended.subscribe(() => {
			this.onPlay(api, index);
		});
	}

	onPlay(api, index) {
		this.user = true;
		this.administartor = true;
		this.players[index].api = api;
	}
}