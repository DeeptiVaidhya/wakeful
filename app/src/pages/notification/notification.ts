import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, LoadingController } from 'ionic-angular';
import { CommunityServiceProvider } from '../../providers/community-service';
import { CommunityDiscussionPage } from '../community-discussion/community-discussion';
/**
 * Generated class for the NotificationPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-notification',
  templateUrl: 'notification.html',
})
export class NotificationPage {
  loading: any;
  result: any;
  notificationList: any = [];
  page = 0;
	totalPage = 0;
  constructor(public navCtrl: NavController, public navParams: NavParams, private communityService: CommunityServiceProvider, public loadCtrl: LoadingController,) {
  }

  // ionViewDidLoad() {
  //   this.NotificationList();
  // }

  ionViewWillEnter() {
		this.getNotificationList();
  }
  
  getNotificationList(){
    this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		this.communityService.notification(this.page).then(
			response => {
				this.loading.dismiss();
        this.result = response;
				if (this.result.status == 'success') {
          this.notificationList = this.result.data;
          this.totalPage = this.result.total_pages;
				}
			},
			err => {
				console.log(err);
				this.loading.dismiss();
			}
		);
  }

  doInfinite(infiniteScroll) {
		this.page = this.page+1;
		setTimeout(() => {
		this.communityService.notification(this.page).then(
			response => {
				this.loading.dismiss();
				this.result = response;
				if (this.result.status == 'success') {
          this.notificationList =  this.notificationList.concat(this.result.data);
					this.totalPage = this.result.total_pages;
				}
			},
			err => {
				console.log(err);
				this.loading.dismiss();
			}
		);
		  infiniteScroll.complete();
		}, 1000);
	  }

  showPost(question_id, post_id, comment_id){
    let data = {'comment_id' : comment_id};
    this.communityService.updateNotification(data).then(response => 
    {
    });
    this.navCtrl.push(CommunityDiscussionPage, { 'question_id': question_id, 'post_id': post_id, 'comment_id' : comment_id });
  }

}
