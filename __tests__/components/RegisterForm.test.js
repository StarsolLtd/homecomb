import React from 'react'
import { shallow } from 'enzyme'
import RegisterForm from '../../assets/js/components/RegisterForm'

it('renders without crashing', () => {
  shallow(<RegisterForm />)
})
