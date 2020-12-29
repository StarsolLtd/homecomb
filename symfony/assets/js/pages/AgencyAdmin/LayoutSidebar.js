import React from 'react';
import {Nav, NavItem, Col} from 'reactstrap';
import {Link} from "react-router-dom";


const LayoutSidebar = () => {
    return (
        <Col md={2} className="light-bronze p-1 pl-4">
            <Nav vertical navbar>
                <NavItem>
                    <Link to='/verified/agency-admin'>Dashboard</Link>
                </NavItem>
                <NavItem>
                    <Link to='/verified/agency'>Update Agency</Link>
                </NavItem>
            </Nav>
        </Col>
    );
};

export default LayoutSidebar;