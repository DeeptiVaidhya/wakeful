import { Component } from '@angular/core';
import { IonicPage, NavController, LoadingController } from 'ionic-angular';
import { Helper } from '../../providers/helper';
import { MenuController } from 'ionic-angular/components/app/menu-controller';
import { AuthServiceProvider } from '../../providers/auth-service';
import { CommunityServiceProvider } from '../../providers/community-service';
import { Storage } from '@ionic/storage';
/**
 * Generated class for the CommunityPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-community-user',
	templateUrl: 'community-user.html',
})
export class CommunityUserPage {
	loading: any;
	title: string = "Community";
	message: string = '';
	comment: string = '';
	data: any = [];
	textbox: any = [];
	breadcrumb: any = [];
	status: String = '';
	replyStatus: String = '';
	question_id = false;
	commentBox = false;
	isWhitespaceComment: any = [];
	isWhitespaceSecComment: any = [];
	isWhitespaceMessage: boolean = false;
	indexValue: any; // for thread reply
	course_id:any = '';
	communityList: any = [];
	result: any;
	page = 0;
	totalPage: number = null;
	ContinueReading:any = [];
	ContinuePostReading:any = [];
	ContinueSecReading:any = [];
	ContinueReadingD:any = [];
	ContinuePostReadingD:any = [];
	ContinueSecReadingD:any = [];
	viewReplies:any = [];
	viewPostReplies:any = [];
	viewSecondReplies:any = [];
	strLength= 350;
	strSecLength= 250;
	strPostLength= 290;
	username = '';
	profilePicture = '';
	access_page_id:any=0;
	spent_time:any=0;
	resource_id:any;
	page_status = 'COMMUNITY'
	redColor = ['A', 'G', 'M', 'S', 'Y'];
	orangeColor = ['B', 'H', 'N', 'T', 'Z'];
	yellowColor = ['C', 'I', 'O', 'U'];
	greenColor = ['D', 'J', 'P', 'V'];
	blueColor = ['E', 'K', 'Q', 'W'];
	purpleColor = ['F', 'L', 'R', 'X'];
	constructor(
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		public menu: MenuController,
		public helper: Helper,
		private storage: Storage, 
		private authService: AuthServiceProvider, 
		private communityService: CommunityServiceProvider,
	) {
		this.authService.get_course_id().then(id => {
			this.course_id = id;
    	});
	}

	ionViewWillEnter() {
		this.page = 0;
		this.getcommunityList(this.page);
		this.storage.get('profile_picture').then(profile_picture => {
			this.profilePicture = profile_picture;
		});
		this.storage.get('username').then(username => {
			this.username = username
		});
		let total = 0;
		this.access_page_id = 0;
		let resource_info = {
			resource_id : this.access_page_id,
			spent_time: total,
			status: this.page_status
		};
		this.onAccessPage(resource_info);
	}

	ionViewDidLeave() {
		let resource_info = {
			resource_id : this.access_page_id,
			spent_time: this.spent_time,
			status: this.page_status
		};	
		this.onAccessPage(resource_info);
		this.access_page_id=0;	
	}

	// Check to show class list or not

	getcommunityList(page) {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		this.communityService.communities(page).then(
			response => {
				this.loading.dismiss();
				this.result = response;
				if (this.result.status == 'success') {
					this.communityList = this.result.data;
					this.totalPage = this.result.total_pages;
					this.ContinueReading = [];
					this.ContinueReadingD = [];
					this.ContinuePostReading = [];
					this.ContinuePostReadingD = [];
					this.ContinueSecReading = [];
					this.ContinueSecReadingD = [];
					this.viewReplies = [];				
					this.viewPostReplies = [];				
					this.viewSecondReplies = [];				
					this.isWhitespaceComment = [];				
					this.isWhitespaceSecComment = [];				
				}
			},
			err => {
				this.loading.dismiss();
			}
		);
	}

	onAccessPage(resource_info) {
		this.authService.accessedResources(resource_info).then(
			result => {				
				this.access_page_id = result['data']['resource_id'];
			},
			err => {console.log(err);}
		);
	}

	doInfinite(infiniteScroll) {
		this.page = this.page+1;
		setTimeout(() => {
			if(this.page < this.totalPage){
				this.communityService.communities(this.page).then(
					response => {
						this.loading.dismiss();
						this.result = response;
						if (this.result.status == 'success') {
							this.communityList = this.communityList.concat(this.result.data);
							this.totalPage = this.result.total_pages;
						}
					},
					err => {
						this.loading.dismiss();
					}
				);
			  infiniteScroll.complete();
			}
		}, 1000);
	}

	doReply(question_id = null, answer_id = null, comment_id = null, comment, commIndex, postRplyIndex) {
		let text = '';
		if (this.message != '') {
			this.isWhitespaceMessage = (this.message || '').trim().length === 0;
			if(this.isWhitespaceMessage){
				return false;
			}
			text = this.message;
		} else if (comment != '' && !comment_id) {
			this.isWhitespaceComment[commIndex] = (comment || '').trim().length === 0;
			if(this.isWhitespaceComment[commIndex]){
				return false;
			}
			text = comment;
		} else if (comment != '' && comment_id) {
			this.isWhitespaceSecComment[postRplyIndex] = (comment || '').trim().length === 0;
			if(this.isWhitespaceSecComment[postRplyIndex]){
				return false;
			}
			text = comment;
		}
		if (text != '') {
			var obj = {
				"answer_id": answer_id,
				"comment": text,
				"parent_comment_id": comment_id,
				"question_id": question_id
			};
			this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
			this.loading.present();
			this.communityService.add_comment(obj).then(
				result => {
					this.data = result;
					this.loading.dismiss();
					if (this.data.status != 'error') {
						this.helper.presentToast(this.data.msg, this.data.status);
						if(this.page == this.totalPage){
							this.page = this.page -1;
						}
						this.getcommunityList(this.page);
						this.comment = '';
						this.message = '';
						this.commentBox = false;
					} else {
						this.helper.presentToast(this.data.msg, 'error');
					}
				}
			);
		}
	}

	setCommentStatus(status, answerId , question_id) {
		if (status != '') {
			this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
			var obj = {
				"answer_id": answerId,
				"status": status,
				"question_id": question_id
			};
			this.loading.present();
			this.communityService.add_comment_status(obj).then(
				result => {
					this.loading.dismiss()
					this.data = result;
					if (this.data.status != 'error') {
						if(this.page == this.totalPage){
							this.page = this.page -1;
						}
						this.getcommunityList(this.page);
					} else {
						this.helper.presentToast(this.data.msg, 'error');
					}
				}
			);
		}
	}

	setReplyStatus(comment_id, status) {
		if (status != '') {
			var obj = {
				"answer_comments_id": comment_id,
				"status": status,
			};
			this.communityService.add_reply_status(obj).then(
				result => {
					this.data = result;
					if (this.data.status != 'error') {
						this.replyStatus = status;
					} else {
						this.helper.presentToast(this.data.msg, 'error');
					}
				},
				err => {
					this.helper.presentToast('Form Invalid', 'error');
				}
			);
		}
	}

	continueReading(commIndex){
		if(!this.ContinueReading[commIndex]){
			this.ContinueReading[commIndex] = true;
		} else {
			this.ContinueReading[commIndex] = !this.ContinueReading[commIndex];
		}
	}

	continueReadingD(dIndex){
		if(!this.ContinueReadingD[dIndex]){
			this.ContinueReadingD[dIndex] = true;
		} else {
			this.ContinueReadingD[dIndex] = !this.ContinueReadingD[dIndex];
		}
	}

	continuePostReading(commIndex){
		if(!this.ContinuePostReading[commIndex]){
			this.ContinuePostReading[commIndex] = true;
		} else {
			this.ContinuePostReading[commIndex] = !this.ContinuePostReading[commIndex];
		}
	}

	continuePostReadingD(dIndex){
		if(!this.ContinuePostReadingD[dIndex]){
			this.ContinuePostReadingD[dIndex] = true;
		} else {
			this.ContinuePostReadingD[dIndex] = !this.ContinuePostReadingD[dIndex];
		}
	}

	continueSecReading(commIndex){
		if(!this.ContinueSecReading[commIndex]){
			this.ContinueSecReading[commIndex] = true;
		} else {
			this.ContinueSecReading[commIndex] = !this.ContinueSecReading[commIndex];
		}
	}

	continueSecReadingD(dIndex){
		if(!this.ContinueSecReadingD[dIndex]){
			this.ContinueSecReadingD[dIndex] = true;
		} else {
			this.ContinueSecReadingD[dIndex] = !this.ContinueSecReadingD[dIndex];
		}
	}

	toggleReplies(commIndex){
		if(!this.viewReplies[commIndex]){
			this.viewReplies[commIndex] = true;
		} else {
			this.viewReplies[commIndex] = !this.viewReplies[commIndex];
		}
	}

	togglePreviousReplies(commIndex){
		if(!this.viewPostReplies[commIndex]){
			this.viewPostReplies[commIndex] = true;
		} else {
			this.viewPostReplies[commIndex] = !this.viewPostReplies[commIndex];
		}
	}

	toggleSecondReplies(postRplyIndex){
		if(!this.viewSecondReplies[postRplyIndex]){
			this.viewSecondReplies[postRplyIndex] = true;
		} else {
			this.viewSecondReplies[postRplyIndex] = !this.viewSecondReplies[postRplyIndex];
		}
	}

	onKeydown(event){
		event.preventDefault();
	}
}


