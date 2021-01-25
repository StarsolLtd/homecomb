import React, {Fragment} from 'react';
import {Link} from "react-router-dom";

const RatedAgency = (props) => {
    return (
        <div className="agency clearfix">
            <div className="float-left agency-logo-container">
                {props.agencyLogoImageFilename &&
                <Link to={'/agency/' + props.agencySlug}>
                    <img src={'/images/images/' + props.agencyLogoImageFilename}
                         className="agency-logo float-left"
                         alt={props.agencyName + ' Logo'}
                    />
                </Link>
                }
            </div>
            <div>
                <p>
                    <a href={'/agency/' + props.agencySlug}>{props.agencyName}</a><br />
                    Average rating: { props.meanRating } stars from { props.ratedCount} review
                    {props.ratedCount > 1 &&
                    <Fragment>s</Fragment>
                    }
                </p>
            </div>
        </div>
    );
}


export default RatedAgency;