import { NgModule } from '@angular/core';
import { AppHeaderComponent } from './app-header/app-header';
import { BackStaticHeaderComponent } from './back-static-header/back-static-header';
import { BreadcrumbComponent } from './breadcrumb/breadcrumb';
import { ModalComponent } from './modal/modal';
import { ProgressBarComponent } from './progress-bar/progress-bar';
import { StaticFooterComponent } from './static-footer/static-footer';
import { MeditationComponent } from './meditation/meditation';

@NgModule({
	declarations: [BackStaticHeaderComponent, ModalComponent, AppHeaderComponent,
    ProgressBarComponent,
    BreadcrumbComponent,
    StaticFooterComponent,
    MeditationComponent],
	imports: [],
	exports: [BackStaticHeaderComponent, ModalComponent, AppHeaderComponent,
    ProgressBarComponent,
    BreadcrumbComponent,
    StaticFooterComponent,
    MeditationComponent],
})
export class ComponentsModule {}
