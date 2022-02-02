import { Component, ViewChild } from '@angular/core';
import { Navbar, IonicPage, NavController, NavParams, LoadingController } from 'ionic-angular';
import { Validators, FormBuilder, FormGroup, FormControl } from '@angular/forms';
import { ClassServiceProvider } from '../../providers/class-service';
import { Helper } from '../../providers/helper';
import { ClassPage } from '../class/class';

@IonicPage()
@Component({
	selector: 'page-question',
	templateUrl: 'question.html',
})
export class QuestionPage {
	@ViewChild(Navbar) navBar: Navbar;
	page_info: any = [];
	page_detail: any = [];
	parent: any;
	loading: any;
	data: any;
	answer_data: any;
	answer: String = '';
	bg_image: String = '';
	private answerForm: FormGroup;
	progress: any;
	disableButton : boolean = false;

	constructor(public helper: Helper, private classService: ClassServiceProvider, public loadCtrl: LoadingController, public navCtrl: NavController, public navParams: NavParams, private formBuilder: FormBuilder) {
		this.page_info = this.navParams.get('page_detail');
		this.parent = this.navParams.get('parent');
		this.page_detail = this.page_info.page_data;
		this.progress = this.page_info.percentage;
		this.answerForm = this.formBuilder.group({
			answer: [this.page_detail.sub_details['answer'], Validators.compose([
				Validators.pattern(/^(?!\s*$).+/),
				Validators.required,
			]),]
		});
	}

	ionViewWillEnter() {
		this.classService.get_background_images().then(res => {
			let data: any = res;
			if (data.hasOwnProperty('inner_page')) {
				this.bg_image = data.inner_page;
			}
		});
	}

	ionViewDidLoad() {
		this.previousPage()
	}

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

	nextPage() {
		var class_id = this.page_info.classes_id;
		var position = this.page_info.position;
		if (this.answerForm.valid) {
			this.disableButton = true;
			var answer = {
				'answer': this.answerForm.value.answer,
				'question_id': this.page_detail.id
			};
			this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
			this.loading.present();
			this.classService.addReflectionAnswer(answer).then(
				result => {
					this.data = result;
					this.loading.dismiss();
					if (this.data.status != 'error') {
						this.parent.getPage(class_id, +position + 1);
					} else {
						this.helper.presentToast(this.data.msg, 'error');
						this.parent.getPage(class_id, +position + 1);
					}
				},
				err => {
					console.log(err);
					this.helper.presentToast('Form Invalid', 'error');
				}
			);
		}  else {
			this.validateAllFormFields(this.answerForm); //{7}
		}
	}

	validateAllFormFields(formGroup: FormGroup) {
		//{1}
		Object.keys(formGroup.controls).forEach(field => {
			//{2}
			const control = formGroup.get(field); //{3}
			if (control instanceof FormControl) {
				//{4}
				control.markAsTouched({ onlySelf: true });
			} else if (control instanceof FormGroup) {
				//{5}
				this.validateAllFormFields(control); //{6}
			}
		});
	}



}
