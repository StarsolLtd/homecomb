import React from 'react';
import {shallow} from 'enzyme';
import CommentForm from "../../assets/js/components/CommentForm";
import LoginOrRegister from "../../assets/js/modals/LoginOrRegister";

it("renders without crashing", () => {
    shallow(<LoginOrRegister />);
});
