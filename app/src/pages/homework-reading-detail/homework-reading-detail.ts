import { Component } from '@angular/core';
import { Events,IonicPage, NavController, NavParams, LoadingController } from 'ionic-angular';
import { Helper } from '../../providers/helper';
import { HomeworkPage } from '../homework/homework';
import { VgAPI } from 'videogular2/core';
import { DomSanitizer } from '@angular/platform-browser';
import { HomeworkServiceProvider } from '../../providers/homework-service';
/**
 * Generated class for the HomeworkReadingDetailPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-homework-reading-detail',
	templateUrl: 'homework-reading-detail.html',
})
export class HomeworkReadingDetailPage {
	full_detail = [];
	exercise_detail = [];
	breadcrumb = [];
	type = '';
	api: VgAPI;
	players = [];
	homework_page = HomeworkPage;
	interval: any;
	// reading_id: any;
	id: any;
	constructor(
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		public navParams: NavParams,
		public helper: Helper,
		public events: Events,
		private sanitizer: DomSanitizer,
		private homeworkService: HomeworkServiceProvider
	) {

		// this.navBar.backButtonClick = () => {
		// 	this.navCtrl.setRoot(ClassPage);
		// }
	}

	// Middleware to check user is valid or not
	ionViewCanEnter() {
		this.helper.authenticated().then(
			response => {
			},
			err => {
			}
		);
	}

	// Call when user enter on the view
	ionViewWillEnter() {
		this.events.publish('homework:disableTabs', true);
		this.full_detail = this.navParams.get('detail');
		this.id = this.full_detail['reading_id']; //reading id 
		this.type = this.navParams.get('type');
		this.exercise_detail = this.navParams.get('exercise_detail');
		this.breadcrumb = ['Homework', this.exercise_detail && this.exercise_detail.hasOwnProperty('title') ? this.exercise_detail['title'] : '', this.full_detail && this.full_detail.hasOwnProperty('title') ? this.full_detail['title'] : ''];
		this.full_detail['reading_detail'] = this.sanitizer.bypassSecurityTrustHtml(this.full_detail['reading_detail']);
		clearInterval(this.interval);
		this.interval = setInterval(() => {
			if(document.hasFocus()){
				this.homeworkService.updateReadingTime({id : this.id }).then(
					response => {
					},
					err => {
					}
				);
			}
		}, 10000);

	}

	ionViewWillLeave() {
		clearInterval(this.interval);
		this.events.publish('homework:disableTabs', false);
	}

	// Event called when the player is ready to play
	onPlayerReady(api: VgAPI) {
		this.api = api;
	}



}
