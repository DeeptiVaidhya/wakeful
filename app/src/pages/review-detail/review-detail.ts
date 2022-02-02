import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, LoadingController, ModalController } from 'ionic-angular';
import { Helper } from '../../providers/helper';
import { MenuController } from 'ionic-angular/components/app/menu-controller';
import { ReviewServiceProvider } from '../../providers/review-service';
import { VgAPI } from 'videogular2/core';
import { ModalComponent } from '../../components/modal/modal';
import { Storage } from '@ionic/storage';
import { ReviewPage } from '../review/review';
import { HomeworkReadingDetailPage } from '../homework-reading-detail/homework-reading-detail';
/**
 * Generated class for the ReviewDetailPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-review-detail',
	templateUrl: 'review-detail.html',
})
export class ReviewDetailPage {
	review_id: any;
	result: any;
	loading: any;
	review_detail: any = [];
	api: VgAPI;
	players = [];
	breadcrumb = [];
	status: boolean = false;
	exercise_detail = [];
	homework_data: any = [];
	av_data: any = [];
	interval : any;
	totalTime;
	rindexVal: any;
	constructor(
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		public navParams: NavParams,
		public menu: MenuController,
		public helper: Helper,
		public modalCtrl: ModalController,
		private reviewService: ReviewServiceProvider,
		public storage: Storage,
	) {
		this.menu.enable(true);
	}

	// Middleware to check user is valid or not
	ionViewCanEnter() {
		this.helper.authenticated().then(
			response => {

			},
			err => {
				console.log("err");
			}
		);
	}

	// Call when user enter on the view
	ionViewWillEnter() {
		this.interval = setInterval(() => {
			if (document.hasFocus()!=true || document.hasFocus()==false) {				
				for(let i=0;i<this.av_data.length;i++){        
					this.players[i].api.pause();
				}
			}else{
			}
		}, 3000);		
		var review_id = this.navParams.get('review_id');
		this.getReviewDetail(review_id);		
	}

	ionViewDidLeave() {
		if(this.players && this.players.hasOwnProperty(this.rindexVal)){
			this.players[this.rindexVal].api.pause();
		}
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

	onPlayerReady(api: VgAPI, index, review) {
		this.api = api;
		this.rindexVal = index;
		this.av_data.push(this.rindexVal);

		if (!this.players[this.rindexVal]) {
			this.players[this.rindexVal] = {};
		}
		this.players[this.rindexVal].api = api;
		if (review.file_status.hasOwnProperty('current_time')) {
			this.api.currentTime = review.file_status.current_time;
		} else {
			this.api.currentTime = 0;
		}

		this.players[this.rindexVal].api.getDefaultMedia().subscriptions.ended.subscribe(() => {
			// Set the video to the beginning
			let obj = {
				time: {
					'current': this.api.currentTime * 1000,
					'total': this.players[this.rindexVal].api.currentTime * 1000,
					'status': 'COMPLETED'
				},
				'reviews_files_id': review.id
			};
			this.onPlay(obj);
			this.players[this.rindexVal].api.getDefaultMedia().currentTime = 0;
		});
	}

	// Get review detail
	getReviewDetail(review_id) {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		this.reviewService.review_detail(review_id).then(
			response => {
				this.loading.dismiss();
				this.result = response;
				if (this.result.status == 'success') {
					var data = this.result.data
					this.review_detail = data;
					var homework_data = this.result.data.homework_data		
					this.homework_data = homework_data;	
					this.breadcrumb = ['Review', this.review_detail.title];
				}
			},
			err => {
				console.log(err);
			}
		);
	}

	getReadingDetail(detail){
		return String(detail).replace(/<.*?>/g, '').trim();
	}

	readingDetail(reading_detail, type) {
		this.navCtrl.push(HomeworkReadingDetailPage, { 'detail': reading_detail, 'exercise_detail': this.exercise_detail, 'type': type });
	}

	// Update audio/video track detail
	onPlay(obj) {		
		let current_time = obj.time.hasOwnProperty('current') ? obj.time.current : 0;
		let left_time = obj.time.hasOwnProperty('left') ? obj.time.left : 0;
		let total =  obj.time.hasOwnProperty('total') ? obj.time.total : 0;
		this.totalTime = obj.time.hasOwnProperty('total') ? obj.time.total : 0;
		let status = obj.time.hasOwnProperty('status') ? obj.time.status : 'STARTED';
		let reviews_files_id = obj.hasOwnProperty('reviews_files_id') ? obj.reviews_files_id : false;
		let reviews_id = obj.hasOwnProperty('reviews_id') ? obj.reviews_id : false;
		let files_id = obj.hasOwnProperty('files_id') ? obj.files_id : false;
		let file_info = {
			current_time: current_time,
			left_time: left_time,
			total_time: total,
			file_status: status,
			reviews_files_id: reviews_files_id,
			reviews_id: reviews_id,
			files_id: files_id,
			classes_id: this.review_detail.classes_id
		};
		this.reviewService.reviewTracking(file_info).then(
			result => {				
			},
			err => {
				console.log(err);
			}
		);
	}


	// Show script content in modal
	showAlert(script, description) {
		let alert = this.modalCtrl.create(ModalComponent, { 'body': description, 'title': script });
		alert.present();
	}
	ngOnInit() { }

	back(){
		this.navCtrl.setRoot(ReviewPage);
	}

}
