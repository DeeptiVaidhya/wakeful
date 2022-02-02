import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { VideoIntroPage } from './video-intro';

@NgModule({
  declarations: [
    VideoIntroPage,
  ],
  imports: [
    IonicPageModule.forChild(VideoIntroPage),
  ],
})
export class VideoIntroPageModule {}
