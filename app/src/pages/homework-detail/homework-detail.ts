import { Component } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { IonicPage, LoadingController, ModalController, NavController, NavParams } from 'ionic-angular';
import { MenuController } from 'ionic-angular/components/app/menu-controller';
import { VgAPI } from 'videogular2/core';
import { ModalComponent } from '../../components/modal/modal';
import { Helper } from '../../providers/helper';
import { HomeworkServiceProvider } from '../../providers/homework-service';
import { AuthServiceProvider } from '../../providers/auth-service';
import { HomeworkReadingDetailPage } from '../homework-reading-detail/homework-reading-detail';
import { MeditationTimerPage } from '../meditation-timer/meditation-timer';

/**
 * Generated class for the HomeworkDetailPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-homework-detail',
	templateUrl: 'homework-detail.html',
})
export class HomeworkDetailPage{
	review_id: any;
	result: any;
	loading: any;
	homework_detail: any = [];
	classList : any;
	api: VgAPI;
	practicePlayer = [];
	homeworkPlayer = [];
	players = [];
	explayers =[];
	exercise_id: false;
	type: false;
	class_id: false;
	exercise_detail = [];
	breadcrumb = [];
	practiceIndexVal: any;
	homeworkIndexVal: any;
	rindexVal:any;
	rindexVal1:any;
	classes_id: any;
	practice_categories_id: any;
	practice_detail :any = [];
	av_data :any = [];
	pr_data :any = [];
	last_avaccess_id:any=0;
	spent_time:any=0;
	access_page_id:any=0;
	page_status = 'PRACTICE';
	resource_id:any;
	interval:any;
	ptype:any;

	constructor(
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		public navParams: NavParams,
		public menu: MenuController,
		public helper: Helper,
		public modalCtrl: ModalController,
		private homeworkService: HomeworkServiceProvider,
		private authService: AuthServiceProvider,
		public sanitizer: DomSanitizer,
	) {
		this.menu.enable(true);
	}

	// Middleware to check user is valid or not
	ionViewCanEnter() {
		this.helper.authenticated().then(
			response => {},
			err => {}
		);
	}

	// Call when user enter on the view
	ionViewWillEnter() {
		this.interval = setInterval(() => {
			if (document.hasFocus()!=true || document.hasFocus()==false) {				
				for(let i=0;i<this.av_data.length;i++){  
					if(this.players && this.players.hasOwnProperty(i)){
						this.players[i].api.pause();
					}
				}

				for(let k=0;k<this.pr_data.length;k++){  
					if(this.explayers && this.explayers.hasOwnProperty(k)){
						this.explayers[k].api.pause();
					}
				}
			}
		}, 1000);			

		var classes_id = this.navParams.get('classes_id');
		this.getclassList(classes_id);
		let total = 0;
		this.access_page_id = 0;
		let resource_info = {
			resource_id : this.access_page_id,
			spent_time: total,
			status: this.page_status
		};	
		this.onAccessPage(resource_info);
	}

	onAccessPage(resource_info) {
		this.authService.accessedResources(resource_info).then(
			result => {				
				this.access_page_id = result['data']['resource_id'];
			},
			err => {console.log(err);}
		);
	}

	getReadingDetail(detail){
		return String(detail).replace(/<.*?>/g, '').trim();
	}

	// Update audio/video and exercise track detail
	onPlay(obj) {
		let current_time = obj.time.hasOwnProperty('current') ? obj.time.current : 0;
		let left_time = obj.time.hasOwnProperty('left') ? obj.time.left : 0;
		let total = obj.time.hasOwnProperty('total') ? obj.time.total : 0;
		let status = obj.time.hasOwnProperty('status') ? obj.time.status : 'STARTED';
		let files_id = obj.hasOwnProperty('files_id') ? obj.files_id : false;
		let practice_categories_id = obj.hasOwnProperty('practice_categories_id') ? obj.practice_categories_id : false;

		let file_info = {
			current_time: current_time,
			left_time: left_time,
			total_time: total,
			file_status: status,
			files_id: files_id,
			practice_categories_id: (practice_categories_id)? practice_categories_id:'10',
			last_avaccess_id: (current_time > 0) ? this.last_avaccess_id : false
		};	
		this.homeworkService.exerciseTracking(file_info).then(
			result => {
				this.last_avaccess_id = result['data']['last_avaccess_id'];
			},
			err => {console.log(err);}
		);
	}

	// Event called when the player is ready to play
	onPlayerReady(api: VgAPI, index, homework) {
		this.api = api;
		this.rindexVal = index;
		this.av_data.push(this.rindexVal);

		if (!this.players[this.rindexVal]) {
			this.players[this.rindexVal] = {};
		}
		this.players[this.rindexVal].api = api;
		this.players[this.rindexVal].api.getDefaultMedia().subscriptions.ended.subscribe(() => {
			// Set the video to the beginning
			let obj = {
				time: {
					'current': this.players[this.rindexVal].api.currentTime * 1000,
					'total': this.players[this.rindexVal].api.currentTime * 1000,
					'status': 'COMPLETED'
				},
				'files_id': homework.files_id,
				'practice_categories_id': homework.practice_categories_id,
				'last_avaccess_id' : this.last_avaccess_id
			};
			this.onPlay(obj);
			this.players[this.rindexVal].api.getDefaultMedia().currentTime = 0;
		});
	}

	onPlayerReadyNew(api: VgAPI, index, pr) {
		this.api = api;
		this.rindexVal1 = index;
		this.pr_data.push(this.rindexVal1);

		if (!this.explayers[this.rindexVal1]) {
			this.explayers[this.rindexVal1] = {};
		}
		this.explayers[this.rindexVal1].api = api;		

		this.explayers[this.rindexVal1].api.getDefaultMedia().subscriptions.ended.subscribe(() => {
			// Set the video to the beginning
			let obj = {
				time: {
					'current':this.explayers[this.rindexVal1].api.currentTime * 1000,
					'total': this.explayers[this.rindexVal1].api.currentTime * 1000,
					'status': 'COMPLETED'
				},
				'files_id': pr.files_id,
				'practice_categories_id': pr.practice_categories_id,
				'last_avaccess_id' : this.last_avaccess_id
			};
			this.onPlay(obj);
			this.explayers[this.rindexVal1].api.getDefaultMedia().currentTime = 0;
		});
	}

	ionViewDidLeave() {
		if(this.players && this.players.hasOwnProperty(this.rindexVal)){
			this.players[this.rindexVal].api.pause();
		}

		if(this.explayers && this.explayers.hasOwnProperty(this.rindexVal1)){
			this.explayers[this.rindexVal1].api.pause();
		}

		this.last_avaccess_id = 0;
		let resource_info = {
			resource_id : this.access_page_id,
			spent_time: this.spent_time,
			status: this.page_status
		};	
		this.onAccessPage(resource_info);
		this.access_page_id=0;
	}

	getclassList(classes_id) {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		var data = {
			'category_id': classes_id
		}
		this.homeworkService.classList(data).then(
			response => {
				this.loading.dismiss();
				this.result = response;
				if (this.result.status == 'success') {
					this.classList = this.result.data.exercise_detail;			
					this.practice_detail =this.result.data.practice_detail;
				}
			},
			err => {
				this.loading.dismiss();

			}
		);
	}
	// Show script content in modal
	showAlert(title, script) {
		let alert = this.modalCtrl.create(ModalComponent, { 'body': script, 'title': title });
		alert.present();
	}

	readingDetail(reading_detail, type) {
		this.navCtrl.push(HomeworkReadingDetailPage, { 'detail': reading_detail, 'exercise_detail': this.exercise_detail, 'type': type });
	}

	homeWorkPlayerReady(api: VgAPI) {
		this.api = api;
	}
	back(){
		this.navCtrl.setRoot(MeditationTimerPage);
	}
}

