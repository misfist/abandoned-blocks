import Bricks from 'bricks.js'

const supportGrid = CSS.supports("grid-template-rows", "masonry");
const container = document.querySelector('.grid');

const sizes = [
    // { columns: 1 },
    { mq: '400px', columns: 2, gutter: 8 },
    { mq: '520px', columns: 3, gutter: 16 },
    { mq: '940px', columns: 4, gutter: 8 },
    { mq: '1200px', columns: 5, gutter: 8 }
];

if (!supportGrid && container) {
    const instance = Bricks({
        container,
        packed: 'data-packed',
        sizes,
        position: false
    });
}
