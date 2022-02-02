import { Component, ViewChild } from '@angular/core';
import { Navbar, IonicPage, ModalController, NavParams, NavController, LoadingController } from 'ionic-angular';
import { VgAPI, VgFullscreenAPI } from 'videogular2/core';
import { ModalComponent } from '../../components/modal/modal';
import { ClassServiceProvider } from '../../providers/class-service';
import { AuthServiceProvider } from '../../providers/auth-service';
import { ClassPage } from '../class/class';
import { Storage } from '@ionic/storage';
import { CONSTANTS } from '../../config/constants';

@IonicPage()
@Component({
	selector: 'page-audio',
	templateUrl: 'audio.html',
})
export class AudioPage {
	@ViewChild(Navbar) navBar: Navbar;
	controls: boolean = false;
	autoplay: boolean = false;
	loop: boolean = false;
	preload: string = 'auto';
	bg_image: string = '';
	api: VgAPI;
	fsAPI: VgFullscreenAPI;
	nativeFs: boolean = true;
	parent: any;
	page_info: any = [];
	page_detail: any = [];
	loading: any;
	audio_detail: any = [];
	file_status: boolean = false;
	progress: any;
	AUDIO_VIDEO_BUTTON_ENABLED: boolean = false;
	result: any;
	course_id:any ='';
	totalTime;
	disableButton : boolean = false;
	last_avaccess_id:any=0;
	leftTime :any=0;
	spentTime: any;
	time: number;
	idleState = 'Not started.';
	interval : any;

	constructor(public classService: ClassServiceProvider, public navCtrl: NavController, public loadCtrl: LoadingController, public modalCtrl: ModalController, private navParams: NavParams, private storage: Storage, private authService: AuthServiceProvider) {
		this.page_info = this.navParams.get('page_detail');
		this.parent = this.navParams.get('parent');
		this.page_detail = this.page_info.page_data;
		this.progress = this.page_info.percentage;
		this.course_id = '';
		this.authService.get_course_id().then(id => {
			this.course_id = id;
		});
	}

	ionViewDidLoad() {
		this.previousPage();
	}

	// Check if class is loaded and go to the current page of class
	ionViewWillEnter() {
		this.interval = setInterval(() => {
			if (document.hasFocus()!=true) {
				this.api.pause();
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
			if(this.course_id){
				if (settings[this.course_id]['AUDIO/VIDEO_BUTTON_CLICKABLE_BEFORE_FINISHING'] == 1) {
					this.AUDIO_VIDEO_BUTTON_ENABLED = true;
				}
			}else{
				if (settings[CONSTANTS.CURRENT_COURSE]['AUDIO/VIDEO_BUTTON_CLICKABLE_BEFORE_FINISHING'] == 1) {
					this.AUDIO_VIDEO_BUTTON_ENABLED = true;
				}
			}
			this.getTrackDetail();
		});
	}

	// Get the previous page in the current class
	previousPage() {
		var class_id = this.page_info.classes_id;
		var position = this.page_info.position;
		this.navBar.backButtonClick = () => {
			if (position == 0) {
				this.navCtrl.setRoot(ClassPage);
			} else {
				this.parent.getPage(class_id, +(position) - 1);
			}
		}
	}

	// When the page to about to leave then update current time of audio (if playing)
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

	// Event called when the player is ready to play
	onPlayerReady(api: VgAPI) {
		this.api = api;
		this.fsAPI = this.api.fsAPI;
		this.api.currentTime = 0;
		this.api.getDefaultMedia().subscriptions.ended.subscribe(($event) => {
			// Set the video to the beginning
			var time = {
				'current': this.api.currentTime * 1000,
				'total': this.totalTime,
				'status': 'COMPLETED',
			}
			this.onPlay(time);
			this.api.getDefaultMedia().currentTime = 0;
			this.file_status = true;
			// add auto finish and got to next page
			if($event.type ==='ended'){
				this.nextPage();
			}
		});
	}
	// Fetching current audio track details
	getTrackDetail() {
		//this.file_status = this.AUDIO_VIDEO_BUTTON_ENABLED;
		let file_info = {
			'user_page_activity_id': this.page_info.page_activity_id,
			'files_id': this.page_detail.files_id,
		};
		this.classService.getTrackTime(file_info).then(
			response => {
				this.audio_detail = response;
				if (this.audio_detail.status == 'success') {
					this.audio_detail = this.audio_detail.data;
					//this.api.currentTime = (this.audio_detail.status == 'COMPLETED') ? 0 : this.audio_detail.current_time;
					this.api.currentTime = this.audio_detail.current_time;
					this.leftTime = this.audio_detail.current_time;
					this.file_status = this.AUDIO_VIDEO_BUTTON_ENABLED ? true : (this.audio_detail.status == 'COMPLETED' ? true : false);
					
				} else {
					this.file_status = this.AUDIO_VIDEO_BUTTON_ENABLED;
				}
			},
			err => {
			}
		);

	}

	onPlay(time) {
		this.totalTime = (time.hasOwnProperty('total')) ? time.total : 0;
		var current_time = (time.hasOwnProperty('current')) ? time.current : 0;
		var left_time = (time.hasOwnProperty('left')) ? time.left : 0;
		var total = this.totalTime;
		var status = (time.hasOwnProperty('status')) ? time.status : 'STARTED';
		var file_info = {
			'current_time': current_time,
			'left_time': left_time,
			'total_time': total ? total : 0,
			'user_page_activity_id': this.page_info.page_activity_id,
			'files_id': this.page_detail.files_id,
			'file_status': status,
			'last_avaccess_id' : (current_time > 0) ? this.last_avaccess_id : false
		};
		this.classService.fileTracking(file_info).then(
			result => {
				this.last_avaccess_id = result['data']['last_avaccess_id'];
			}, err => {
			}
		)
	}

	showAlert() {
		let alert = this.modalCtrl.create(ModalComponent, { 'body': this.page_detail.script, 'title': this.page_info.title });
		alert.present();
	}	

	nextPage() {
		if (this.file_status) {
			this.disableButton = true;
			var class_id = this.page_info.classes_id;
			var position = this.page_info.position;
			this.parent.getPage(class_id, +position + 1);
		}
	}
}
