import { Component, ViewChild } from '@angular/core';
import { Navbar, IonicPage, NavController, NavParams, LoadingController } from 'ionic-angular';
import { Validators, FormBuilder, FormGroup } from '@angular/forms';
import { ClassServiceProvider } from '../../providers/class-service';
import { Helper } from '../../providers/helper';
import { ClassPage } from '../class/class';



/**
 * Generated class for the IntentionPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-intention',
	templateUrl: 'intention.html',
})
export class IntentionPage {
	@ViewChild(Navbar) navBar: Navbar;
	page_info: any = [];
	page_detail: any = [];
	parent: any;
	private intentionForm: FormGroup;
	loading: any;
	bg_image: String='';
	data: any;
	progress:any;
	disableButton : boolean = false;

	constructor(
		public helper: Helper,
		private classService: ClassServiceProvider,
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		public navParams: NavParams,
		private formBuilder: FormBuilder
	) {
		this.page_info = this.navParams.get('page_detail');
		this.parent = this.navParams.get('parent');
		this.page_detail = this.page_info.page_data;
		this.progress = this.page_info.percentage;
		this.intentionForm = this.formBuilder.group({
			intention: [this.page_detail.sub_details['intention'],Validators.compose([
				Validators.pattern(/^(?!\s*$).+/),
				Validators.required,
			]),],
		});
	}
	ionViewWillEnter() {
		this.classService.get_background_images().then(res=>{
			let data:any=res;
			if(data.hasOwnProperty('inner_page')){
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
		if (this.intentionForm.valid) {
			this.disableButton = true;
			var intention = {
				intention: this.intentionForm.value.intention,
				intention_id: this.page_detail.id,
			};
			this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
			this.loading.present();
			this.classService.addIntention(intention).then(
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
					this.loading.dismiss();
					console.log(err);
					this.helper.presentToast('Form Invalid', 'error');
				}
			);
		}
	}
}
