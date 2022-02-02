import { Component } from '@angular/core';
import { IonicPage, NavController, LoadingController, NavParams } from 'ionic-angular';
import { Helper } from '../../providers/helper';
import { MenuController } from 'ionic-angular/components/app/menu-controller';
import { CommunityServiceProvider } from '../../providers/community-service';
import { Storage } from '@ionic/storage';
import { NotificationPage } from '../notification/notification';

/**
 * Generated class for the CommunityPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-community-discussion',
	templateUrl: 'community-discussion.html',
})
export class CommunityDiscussionPage {
	loading: any;
	title: string = "Community";
	message: string = '';
	comment: string = '';
	data: any = [];
	textbox: any = [];
	replyStatus: String = '';
	question_id = null;
	post_id = null;
	comment_id = null;
	commentBox = false;
	isWhitespaceComment: any = [];
	isWhitespaceSecComment: any = [];
	isWhitespaceMessage: boolean = false;
	indexValue: any; // for thread reply
	course_id:any = '';
	communityList: any = [];
	result: any;
	page = 0;
	totalPage = 0;
	ContinueReading:any = [];
	ContinuePostReading:any = [];
	ContinueSecReading:any = [];
	viewReplies:any = [];
	viewPostReplies:any = [];
	viewSecondReplies:any = [];
	strLength= 350;
	strSecLength= 250;
	strPostLength= 290;
	username = '';
	profilePicture = '';
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
		private navParams: NavParams,
		private storage: Storage, 
		private communityService: CommunityServiceProvider,
	) {
		this.menu.enable(true);
		this.question_id = this.navParams.get('question_id');
		this.post_id = this.navParams.get('post_id');
		this.comment_id = this.navParams.get('comment_id');
		
	}

	back(){
		this.navCtrl.setRoot(NotificationPage);
	}

	ionViewWillEnter() {
		let data = { 'question_id': this.question_id, 'post_id': this.post_id}
		this.getcommunity(data);
		this.storage.get('profile_picture').then(profile_picture => {
			this.profilePicture = profile_picture
		});
		this.storage.get('username').then(username => {
			this.username = username
		});
	}

	// Check to show class list or not

	getcommunity(question_id) {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		this.communityService.community(question_id).then(
			response => {
				this.loading.dismiss();
				this.result = response;
				if (this.result.status == 'success') {
					this.communityList = this.result.data;
					this.totalPage = this.result.total_pages;
					this.ContinueReading = [];
					this.ContinuePostReading = [];
					this.ContinuePostReading = [];
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
						let data = { 'question_id': this.question_id, 'post_id': this.post_id}
						this.getcommunity(data);
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

	replyClick(index) {
		this.isWhitespaceMessage = false;
		this.commentBox = false;
		this.message = '';
		this.indexValue = index;
		this.textbox.forEach((element, ind) => {
			ind != index && (this.textbox[ind] = false);
		});
		if (!this.textbox[index]) {
			this.textbox[index] = true;
		} else {
			this.textbox[index] = !this.textbox[index];
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
						let data = { 'question_id': this.question_id, 'post_id': this.post_id}
						this.getcommunity(data);
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

	continuePostReading(commIndex){
		if(!this.ContinuePostReading[commIndex]){
			this.ContinuePostReading[commIndex] = true;
		} else {
			this.ContinuePostReading[commIndex] = !this.ContinuePostReading[commIndex];
		}
	}

	continueSecReading(commIndex){
		if(!this.ContinueSecReading[commIndex]){
			this.ContinueSecReading[commIndex] = true;
		} else {
			this.ContinueSecReading[commIndex] = !this.ContinueSecReading[commIndex];
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

	makeRead(comment_id){
		if(comment_id == this.comment_id){
			let data = {'comment_id' : comment_id};
			this.communityService.updateNotification(data).then(response => 
			{
			});
		}
	}
}

