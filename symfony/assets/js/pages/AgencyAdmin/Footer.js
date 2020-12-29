import React from "react";
import {Link} from "react-router-dom";

const Footer = (props) => {
    return (
        <footer className="mt-auto navbar navbar-expand-sm">
            <ul className="list-inline text-center">
                {props.user &&
                    <li className="list-inline-item"><a href="/logout" className="nav-link">Log Out</a></li>
                }
                {!props.user &&
                    <li className="list-inline-item"><a href="/login" className="nav-link">Log In</a></li>
                }
            </ul>
        </footer>
    );
}

export default Footer;