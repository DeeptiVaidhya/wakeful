import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { HomeworkPage } from './homework';

@NgModule({
  declarations: [
    HomeworkPage,
  ],
  imports: [
    IonicPageModule.forChild(HomeworkPage),
  ],
})
export class HomeworkPageModule {}
