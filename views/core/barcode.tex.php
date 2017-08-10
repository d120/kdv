\documentclass[a4paper,11pt]{scrartcl}
\usepackage{GS1}
\usepackage{multicol}
\usepackage[utf8]{inputenc}
\usepackage{eurosym}
\usepackage[left=10mm,right=10mm,top=15mm,bottom=15mm]{geometry}


\newcommand{\product}[3]{%
\begin{tabular}{c}
\sffamily{\textbf{\Large{#1 (#2 \euro)}}}\\
\EANBarcode[module_width=0.5mm,module_height=15mm,code=EAN-13]{#3}\\
\vspace{8mm}
\end{tabular}}


\pagestyle{empty}
\setlength{\parindent}{0pt}



\begin{document}


\begin{multicols}{2}
\centering

<?php foreach($products as $p): if($p["position"]==99999)continue; ?>
\product{<?=latexSpecialChars( $p['name'] )?>}{<?= sprintf("%.02f", $p['price']/100) ?>}{<?= $p['code'] ?>}
<?php endforeach; ?>

\end{multicols}
\end{document}

