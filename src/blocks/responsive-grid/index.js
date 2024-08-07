import { columns as icon } from '@wordpress/icons';

import initBlock from '../init-block';
import edit from './edit';
import metadata from './block.json';
import save from './save';

import './style.scss';

const name = metadata.name;

const settings = { icon, edit, save };

initBlock( { name, metadata, settings } );
