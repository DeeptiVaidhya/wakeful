import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { CommunityDiscussionPage } from './community-discussion';

@NgModule({
  declarations: [
    CommunityDiscussionPage,
  ],
  imports: [
    IonicPageModule.forChild(CommunityDiscussionPage),
  ],
})
export class CommunityDiscussionPageModule {}
