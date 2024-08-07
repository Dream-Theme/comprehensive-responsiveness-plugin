import { column as icon } from '@wordpress/icons';

import initBlock from '../init-block';
import edit from './edit';
import metadata from './block.json';
import save from './save';

const name = metadata.name;

const settings = { icon, edit, save };

initBlock( { name, metadata, settings } );
