import React from 'react';
import {shallow} from 'enzyme';
import CommentForm from "../../assets/js/components/CommentForm";

it("renders without crashing", () => {
    const user = {
        firstName: 'Jane',
        lastName: 'Doe'
    }
    shallow(<CommentForm user={user} />);
});
