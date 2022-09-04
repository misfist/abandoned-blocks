import Masonry from 'masonry-layout';

const supportGrid = CSS.supports("grid-template-rows", "masonry");
const container = document.querySelector('.grid');

if(!supportGrid && container) {
  const masonry = new Masonry( container, {
    itemSelector: '.wp-block-post',
    columnWidth: 200
  });
}

// // init with selector
// var masonry = new Masonry( '.grid', {
//   // options...
// });