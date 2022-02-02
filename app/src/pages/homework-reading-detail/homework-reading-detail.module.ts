import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { HomeworkReadingDetailPage } from './homework-reading-detail';

@NgModule({
  declarations: [
    HomeworkReadingDetailPage,
  ],
  imports: [
    IonicPageModule.forChild(HomeworkReadingDetailPage),
  ],
})
export class HomeworkReadingDetailPageModule {}
