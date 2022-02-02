import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { CommunityUserPage } from './community-user';

@NgModule({
  declarations: [
    CommunityUserPage,
  ],
  imports: [
    IonicPageModule.forChild(CommunityUserPage),
  ],
})
export class CommunityUserPageModule {}
