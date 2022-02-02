import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { WelcomeVideoPage } from './welcome-video';

@NgModule({
  declarations: [
    WelcomeVideoPage,
  ],
  imports: [
    IonicPageModule.forChild(WelcomeVideoPage),
  ],
})
export class WelcomeVideoPageModule {}
