import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { ClassSchedulePage } from './class-schedule';

@NgModule({
  declarations: [
    ClassSchedulePage,
  ],
  imports: [
    IonicPageModule.forChild(ClassSchedulePage),
  ],
})
export class ClassSchedulePageModule {}
