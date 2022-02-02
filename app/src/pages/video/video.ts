import { Component } from '@angular/core';
import { Storage } from '@ionic/storage';
import { IonicPage, LoadingController, ModalController, NavController, NavParams } from 'ionic-angular';
import { VgAPI } from 'videogular2/core';
import { ModalComponent } from '../../components/modal/modal';
import { CONSTANTS } from '../../config/constants';
import { AuthServiceProvider } from '../../providers/auth-service';
import { ClassServiceProvider } from '../../providers/class-service';

/**
 * Generated class for the VideoPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-video',
	templateUrl: 'video.html',
})
export class VideoPage {
	controls: boolean = false;
	api: VgAPI;
	parent: any;
	page_info: any = [];
	page_detail: any = [];
	file_status: boolean = false;
	AUDIO_VIDEO_BUTTON_ENABLED: boolean = false;
	loading: any;
	video_detail: any = [];
	progress: any;
	result: any;
	bg_image: String = '';
	course_id: any = '';
	totalTime;
	disableButton : boolean = false;
	last_avaccess_id:any=0;
	interval : any;
	
	constructor(
		public classService: ClassServiceProvider,
		private authService: AuthServiceProvider,
		public navCtrl: NavController,
		public loadCtrl: LoadingController,
		public modalCtrl: ModalController,
		private navParams: NavParams,
		private storage: Storage
	) {
		this.page_info = this.navParams.get('page_detail');
		this.page_detail = this.page_info.page_data;
		this.progress = this.page_info.percentage;
		this.parent = this.navParams.get('parent');
		this.course_id = '';
		this.authService.get_course_id().then(id => {
			this.course_id = id;
		});
	}


	// Check if class is loaded and go to the current page of class
	ionViewWillEnter() {
		this.interval = setInterval(() => {
			if (document.hasFocus()!=true) {
				this.api.pause();
			}else{
			}
		}, 3000);
		this.classService.get_background_images().then(res => {
			let data: any = res;
			if (data.hasOwnProperty('inner_page')) {
				this.bg_image = data.inner_page;
			}
		});
		this.storage.get('course_settings').then((settings) => {
			this.AUDIO_VIDEO_BUTTON_ENABLED = false;
			if (this.course_id) {
				if (settings[this.course_id]['AUDIO/VIDEO_BUTTON_CLICKABLE_BEFORE_FINISHING'] == 1) {
					this.AUDIO_VIDEO_BUTTON_ENABLED = true;
				}
			} else {
				if (settings[CONSTANTS.CURRENT_COURSE]['AUDIO/VIDEO_BUTTON_CLICKABLE_BEFORE_FINISHING'] == 1) {
					this.AUDIO_VIDEO_BUTTON_ENABLED = true;
				}
			}
			this.getTrackDetail();
		});
	}	

	ionViewDidLeave() {
		this.api.pause();
		this.api.getDefaultMedia().subscriptions.pause.subscribe(() => {
			// Set the video to the beginning
			var time = {
				'current': this.api.currentTime * 1000,
				'total': this.totalTime,
			}
			this.onPlay(time);
		});
	}


	// Player is ready and video is about to play
	onPlayerReady(api: VgAPI) {
		this.api = api;
		this.api.getDefaultMedia().subscriptions.ended.subscribe(($event) => {
			// Set the video to the beginning
			var time = {
				current: this.api.currentTime * 1000,
				total: this.totalTime,
				status: 'COMPLETED',
			};
			this.onPlay(time);
			this.api.getDefaultMedia().currentTime = 0;
			this.file_status = true;
			if($event.type==='ended'){
				this.nextPage();
			}
		});
	}

	getPlayerStatus() {
		return this.api && this.api.state;
	}

	// Get current video details
	getTrackDetail() {
		var file_info = {
			user_page_activity_id: this.page_info.page_activity_id,
			files_id: this.page_detail.files_id,
		};
		this.classService.getTrackTime(file_info).then(
			response => {
				this.video_detail = response;
				if (this.video_detail.status == 'success') {
					this.video_detail = this.video_detail.data;
					//this.api.currentTime = this.video_detail.status == 'COMPLETED' ? 0 : this.video_detail.current_time;
					this.api.currentTime = this.video_detail.current_time;
					//this.file_status = this.video_detail.status == 'COMPLETED' ? true : false;
					this.file_status = this.AUDIO_VIDEO_BUTTON_ENABLED ? true : (this.video_detail.status == 'COMPLETED' ? true : false);
				} else {
					this.file_status = this.AUDIO_VIDEO_BUTTON_ENABLED;
				}
			},
			err => {
			}
		);
	}

	// On play event to capture current time of video
	onPlay(time) {
		this.totalTime = time.hasOwnProperty('total') ? time.total : 0;
		var current_time = time.hasOwnProperty('current') ? time.current : 0;
		var file_info = {
			current_time: current_time,
			left_time: time.hasOwnProperty('left') ? time.left : 0,
			total_time: this.totalTime ,
			user_page_activity_id: this.page_info.page_activity_id,
			files_id: this.page_detail.files_id,
			file_status: time.hasOwnProperty('status') ? time.status : 'STARTED',
			last_avaccess_id : (this.last_avaccess_id > 0) ? this.last_avaccess_id : false
		};
		this.classService.fileTracking(file_info).then(
			result => {
				this.last_avaccess_id = result['data']['last_avaccess_id'];
			},
			err => {
			}
		);
	}

	showAlert(script) {
		let alert = this.modalCtrl.create(ModalComponent, {
			body: this.page_detail.script,
			title: this.page_info.title,
		});
		alert.present();
	}

	ngOnInit() { }

	// Calling next page of class
	nextPage() {
		if (this.file_status) {
			this.disableButton = true;
			this.parent.nextPage();
		}
	}
}
