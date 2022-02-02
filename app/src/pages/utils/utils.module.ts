import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { UtilsPage } from './utils';

@NgModule({
  declarations: [
    UtilsPage,
  ],
  imports: [
    IonicPageModule.forChild(UtilsPage),
  ],
})
export class UtilsPageModule {}
