#!/bin/bash
for url in {https://raw.github.com/samizdatco/arbor/master/demos/halfviz/src/renderer.js,https://raw.github.com/samizdatco/arbor/master/lib/arbor.js,https://raw.github.com/samizdatco/arbor/master/demos/_/graphics.js,https://raw.github.com/nnnick/Chart.js/master/Chart.min.js}; do
	rm ${url##h*/}
	wget ${url}
done
