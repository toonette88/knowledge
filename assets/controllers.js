import { application } from '@hotwired/stimulus';
import HelloController from './controllers/hello_controller.js';

application.register('hello', HelloController);
