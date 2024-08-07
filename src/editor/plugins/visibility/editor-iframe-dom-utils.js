/**
 * Insert a node after element referenceNode is the node you want to put newNode after.
 * @param {*} referenceNode is the node you want to put newNode after.
 * @param {*} newNode       the node you want to insert
 */
export function insertAfter( referenceNode, newNode ) {
	if ( ! referenceNode || ! newNode ) return;
	referenceNode.parentNode.insertBefore( newNode, referenceNode.nextSibling );
}

/**
 * Insert a node before element referenceNode is the node you want to put newNode before.
 * @param {*} referenceNode is the node you want to put newNode before.
 * @param {*} newNode       the node you want to insert
 */
export function insertBefore( referenceNode, newNode ) {
	if ( ! referenceNode || ! newNode ) return;
	referenceNode.parentNode.insertBefore( newNode, referenceNode );
}

const getEditorIframe = () => {
	return document.querySelector( 'iframe[name^="editor-canvas"]' );
};

export function getEditorDocument() {
	return getEditorIframe()?.contentWindow?.document ?? document;
}
