import React, {Fragment} from "react";

const LogInOurOutNavLinks = (props) => {
    return (
        <Fragment>
            {props.user &&
            <li className="list-inline-item"><a href="/logout" className="nav-link">Log Out</a></li>
            }
            {!props.user &&
            <li className="list-inline-item"><a href="/login" className="nav-link">Log In</a></li>
            }
        </Fragment>
    );
}

export default LogInOurOutNavLinks;