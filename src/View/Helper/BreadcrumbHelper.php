<?php

namespace App\View\Helper;
use Cake\View\Helper;

class BreadcrumbHelper extends Helper {

    /**
     * @param string $label
     * @param \Cake\View\Helper\HtmlHelper $htmlHelper
     *
     * In order to make a trail of breadcrumbs we must:
     * 1. Emit any number of trail elements, in a view, using $htmlHelper->addCrumb().
     * 2. Emit the beginning of the trail in a layout, using $thmlHelper->getCrumbs().
     * 3. addCrumbs and getCrumbs provide the necessary information so that when the time
     *    comes to actually render the trail, the Cake software can do this.
     *
     * The problem with this plan is that some views can be reached via more than one path.
     * How can the view know which path led to it?  The basic answer is to save a path
     * in the session, with each step towards a particular view, and use that path to
     * generate the trail using a suitable number of invocations of addCrumb.  Each view's
     * url is added to the session trail, as well as a suitable label to be used in the trail.
     */
    public function makeTrail($label,$htmlHelper) {

        // 1. Read the present trail or init if none.
        $sessionVar='breadcrumbs';
        //$this->request->session()->delete($sessionVar);
        $sessionCrumbs=$this->request->session()->read($sessionVar);
        if(is_null($sessionCrumbs))$sessionCrumbs=[];

        // 2. Get the present url and parse into a parameter array
        $requestUrl=$this->request->url;
        $requestUrlParams=\Cake\Routing\Router::parse($requestUrl);

        // 3. Build a new trail array by looking for the existing
        // url in the existing trail array.  This effectively removes
        // any elements of the existing trail array, that are after the
        // present url.
        $newArray=[];
        foreach($sessionCrumbs as $key=>$crumb) {
            if($key==$requestUrl)
                break;
            else
                $newArray[$key]=$crumb;
        }

        // 3.1 Whether this is new or the last item matched, add it here
        $newArray[$requestUrl]=['label'=>$label,'params'=>$requestUrlParams];

        // 4. Save the trail to the session
        $this->request->session()->write($sessionVar,$newArray);

        // 5. Now add the crumbs the ordinary way
        foreach($newArray as $key=>$crumb){
            if($key==$requestUrl) break; // no crumb for this url
            $htmlHelper->addCrumb($crumb['label'], $crumb['params']);
        }
    }
}
