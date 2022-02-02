import { Component } from '@angular/core';
import { NavController, LoadingController, IonicPage } from 'ionic-angular';
import { ClassServiceProvider } from '../../providers/class-service';
import { Helper } from '../../providers/helper';
import { Validators, FormBuilder, FormGroup } from '@angular/forms';
/**
 * Generated class for the FeedbackPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-feedback',
	templateUrl: 'feedback.html',
})
export class FeedbackPage {
	questionList: any;
	loading: any;
	result: any;
	private feedbackForm: FormGroup;
	answer: any[] = [];
	model: any = {};
	error: any = {};

	constructor(
		private classService: ClassServiceProvider,
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		public helper: Helper,
		private formBuilder: FormBuilder
	) {
		this.feedbackForm = this.formBuilder.group({
			feedback_answers: ['', Validators.required],
			question_id: ['', Validators.required],
			courses_id: ['', Validators.required],
		});
		this.getFeedback();
	}

	ionViewCanEnter() {
		this.helper.authenticated().then(
			response => {
			},
			err => {
			}
		);
	}

	getFeedback() {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		this.classService.feedback().then(
			response => {
				this.loading.dismiss();
				this.result = response;
				if (this.result.status == 'success') {
					this.questionList = this.result.data;
				}
			},
			err => {
				this.loading.dismiss();
			}
		);
	}

	save() {
		let data: Object = { feedback_answers: [], question_id: [], course_id: [] },
			feedback_answers = document.querySelectorAll('textarea[name^=feedback_answers]'),
			question_ids = document.querySelectorAll('input[name^=question_id]');
			//course_ids = document.querySelectorAll('input[name^=course_id]');

		for (let i = 0, ans = feedback_answers, len = ans.length; i < len; i++) {
			data['feedback_answers'][i] = ans[i]['value'];
			data['question_id'][i] = question_ids[i]['value'];
			//data['course_id'][i] = course_ids[i]['value'];
		}
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		this.classService.save_feedback(data).then(
			response => {
				this.loading.dismiss();
				this.result = response;
				if (this.result.status == 'success') {
					this.helper.presentToast(this.result.msg, this.result.status);
					this.navCtrl.setRoot(this.navCtrl.getActive().component);
					this.error = {};
				} else {
					this.error = this.result.data;
				}
			},
			err => {
				this.loading.dismiss();
			}
		);
	}
}
