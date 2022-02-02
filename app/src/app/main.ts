import { enableProdMode } from '@angular/core';
import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { CONSTANTS } from '../config/constants';
import { AppModule } from './app.module';
if(CONSTANTS.ENV.PROD===true){
	enableProdMode();
}
platformBrowserDynamic().bootstrapModule(AppModule);
