import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs/BehaviorSubject';

@Injectable()
export class DataServiceProvider {
	private messageSource = new BehaviorSubject<number>(0);
	currentClass = this.messageSource.asObservable();

	constructor() {}

	changeClass(classId: number) {
		this.messageSource.next(classId);
	}
}
