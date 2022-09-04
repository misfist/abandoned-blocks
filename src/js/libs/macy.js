import Macy from "macy";

const supportGrid = CSS.supports("grid-template-rows", "masonry");
const containerEl = document.querySelector('.grid');

if (!supportGrid && containerEl) {
    const macyInstance = Macy({
        container: containerEl,
        columns: 5,
        margin: 16,
        trueOrder: true,
        breakAt: {
            1200: 5,
            1100: 4,
            928: 3,
            710: 2,
            528: 1
        }
    });

    macyInstance.runOnImageLoad(function () {
        macyInstance.recalculate(true, true);
        const event = document.createEvent('UIEvents');
        event.initUIEvent('resize', true, false, window, 0);
        window.dispatchEvent(event);
    }, true);
}
