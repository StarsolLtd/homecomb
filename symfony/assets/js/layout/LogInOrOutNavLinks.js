import React, {Fragment} from "react";

const LogInOurOutNavLinks = (props) => {
    return (
        <Fragment>
            {props.user &&
            <li><a href="/logout" className={props.className}>Log Out</a></li>
            }
            {!props.user &&
                <Fragment>
                    <li><a href="/login" className={props.className}>Log In</a></li>
                    <li><a href="/register" className={props.className + ' register-link'}>Register</a></li>
                </Fragment>
            }
        </Fragment>
    );
}

export default LogInOurOutNavLinks;