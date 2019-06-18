import { isEmpty, has } from 'lodash';
import moment from 'moment';
import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import FullCalendar from '@fullcalendar/react';
import dayGridPlugin from '@fullcalendar/daygrid';
import { Container, Row, Col, Form, FormGroup, Label,Input, FormFeedback, Button } from 'reactstrap';
import { DatePicker, DatePickerInput } from 'rc-datepicker';
import { ToastContainer, toast } from 'react-toastify';

import 'bootstrap/dist/css/bootstrap.min.css';
import '@fullcalendar/core/main.css';
import '@fullcalendar/daygrid/main.css';
import 'rc-datepicker/lib/style.css';
import 'react-toastify/dist/ReactToastify.min.css';

export default class Example extends Component {

  constructor(props) {
      super(props);
      this.calendarComponentRef = React.createRef();

      this.state = {
        form: {},
        errors: [],
        calendarEvents: [],
        defaultMonthStartDate: moment().startOf('month').format('YYYY-MM-DD'),
        defaultMonthEndDate: moment().endOf('month').format('YYYY-MM-DD'),
        daysOfTheWeeks: {
          monday: false,
          tuesday: false,
          wednesday: false,
          thursday: false,
          friday: false,
          saturday: false,
          sunday: false
        }
      };
  }

  componentDidMount() {
    this.loadData();
  }

  loadData() {
    const {defaultMonthStartDate, defaultMonthEndDate} = this.state;
    axios({
      method: 'GET',
      url: `api/events?start_date=${defaultMonthStartDate}&end_date=${defaultMonthEndDate}`
    }).then(
        (response) => {
            this.setState({
                calendarEvents : response.data.data
            });
        }
      );
  }

  previousMonth = () => {
    let calendarApi = this.calendarComponentRef.current.getApi()
    calendarApi.prev();
    this.setCalendarDateRange(calendarApi.getDate());
  }

  nextMonth = () => {
    let calendarApi = this.calendarComponentRef.current.getApi()
    calendarApi.next();
    this.setCalendarDateRange(calendarApi.getDate());
  }

  setCalendarDateRange(currentMonthDate) {
    this.setState(
      {
          defaultMonthStartDate: moment(currentMonthDate).format('YYYY-MM-DD'),
          defaultMonthEndDate: moment(currentMonthDate).endOf('month').format('YYYY-MM-DD')
      },
      () => { this.loadData() }
    );
  }

  formValidationState = (field) => {
    if(this.state.hasOwnProperty('errors')){
      const errors = this.state.errors;
      return !isEmpty(errors) && has(errors, field);
    }
  }

  onFormChange = (key, value) => {
    const { form } = this.state;
    let formClone = Object.assign({}, form);
    formClone[key] = value;

    this.setState({
        form : formClone
    });
  }

  onDaysOfTheWeekUpdate = (key) => {
    const { daysOfTheWeeks, form } = this.state;
    let daysOfTheWeeksClone = Object.assign({}, daysOfTheWeeks);
    let formClone = Object.assign({}, form);
    daysOfTheWeeksClone[key] = daysOfTheWeeksClone[key] ? false : true;
    formClone['days_of_the_weeks'] = [];
    Object.keys(daysOfTheWeeksClone).forEach(function (item) {
      if(daysOfTheWeeksClone[item]){
        formClone['days_of_the_weeks'].push(item);
      }
    });
    this.setState({
        daysOfTheWeeks : daysOfTheWeeksClone,
        form: formClone
    });
  }

  clearForm = () => {
    this.setState({
        form : {},
        errors: [],
        daysOfTheWeeks: {
          monday: false,
          tuesday: false,
          wednesday: false,
          thursday: false,
          friday: false,
          saturday: false,
          sunday: false
        }
    });
  }

  submit = () => {
    let formData = this.state.form;

    if(formData.hasOwnProperty('start_date')){
      formData.start_date = moment(formData.start_date).format('YYYY-MM-DD');
    }

    if(formData.hasOwnProperty('end_date')){
      formData.end_date = moment(formData.end_date).format('YYYY-MM-DD');
    }

    axios({
      method: 'POST',
      url: `api/events`,
      data: formData,
      validateStatus: (status) => {
        return true;
      }
    }).then(
      (response) => {
        if(response.status === 200) {
          toast.success(response.data.message, {
            position: toast.POSITION.TOP_RIGHT
          });
          this.clearForm();
          this.loadData();
        }

        if(response.status === 422) {
          this.setState({
              errors : response.data.errors
          });
        }
      }
    );
  }
  render() {
    const {form, errors, calendarEvents, daysOfTheWeeks} = this.state;

    return (
      <div>
        <ToastContainer />
        <Container>
          <h1>Event Scheduler</h1>
          <Row>
            <Col md={4}>
              <Form>
                <FormGroup>
                  <Label for="event">Event</Label>
                  <Input
                    type="text"
                    name="event"
                    id="event"
                    value={form.event || ''}
                    invalid={this.formValidationState('event')}
                    onChange={event => this.onFormChange('event',event.target.value)}
                  />
                  <FormFeedback>{this.formValidationState('event') ? errors.event[0] : ''}</FormFeedback>
                </FormGroup>
                <FormGroup>
                  <Label>Start Date</Label>
                  <DatePickerInput
                    className='my-custom-datepicker-component'
                    value={form.start_date || null}
                    onChange={(value)=> this.onFormChange('start_date',value)}
                  />
                  <FormFeedback style={{display: this.formValidationState('start_date') ? 'block' : 'none'}}>{this.formValidationState('start_date') ? errors.start_date[0] : ''}</FormFeedback>
                </FormGroup>
                <FormGroup>
                  <Label>End Date</Label>
                  <DatePickerInput
                    className='my-custom-datepicker-component'
                    value={form.end_date || null}
                    disabled={!form.hasOwnProperty('start_date') ? true : false}
                    onChange={(value)=> this.onFormChange('end_date',value)}
                  />
                  <FormFeedback style={{display: this.formValidationState('end_date') ? 'block' : 'none'}}>{this.formValidationState('end_date') ? errors.end_date[0] : ''}</FormFeedback>
                </FormGroup>
                <FormGroup check>
                  <Label check>
                    <Input
                      type="checkbox"
                      value={daysOfTheWeeks.monday}
                      disabled={!form.hasOwnProperty('end_date') ? true : false}
                      onChange={event => this.onDaysOfTheWeekUpdate('monday',event.target.value)}
                    /> Monday
                  </Label>
                </FormGroup>
                <FormGroup check>
                  <Label check>
                    <Input
                      type="checkbox"
                      value={daysOfTheWeeks.tuesday}
                      disabled={!form.hasOwnProperty('end_date') ? true : false}
                      onChange={event => this.onDaysOfTheWeekUpdate('tuesday',event.target.value)}
                    /> Tuesday
                  </Label>
                </FormGroup>
                <FormGroup check>
                  <Label check>
                    <Input
                      type="checkbox"
                      value={daysOfTheWeeks.wednesday}
                      disabled={!form.hasOwnProperty('end_date') ? true : false}
                      onChange={event => this.onDaysOfTheWeekUpdate('wednesday',event.target.value)}
                    /> Wednesday
                  </Label>
                </FormGroup>
                <FormGroup check>
                  <Label check>
                    <Input
                      type="checkbox"
                      value={daysOfTheWeeks.thursday}
                      disabled={!form.hasOwnProperty('end_date') ? true : false}
                      onChange={event => this.onDaysOfTheWeekUpdate('thursday',event.target.value)}
                    /> Thursday
                  </Label>
                </FormGroup>
                <FormGroup check>
                  <Label check>
                    <Input
                      type="checkbox"
                      value={daysOfTheWeeks.friday}
                      disabled={!form.hasOwnProperty('end_date') ? true : false}
                      onChange={event => this.onDaysOfTheWeekUpdate('friday',event.target.value)}
                    /> Friday
                  </Label>
                </FormGroup>
                <FormGroup check>
                  <Label check>
                    <Input
                      type="checkbox"
                      value={daysOfTheWeeks.saturday}
                      disabled={!form.hasOwnProperty('end_date') ? true : false}
                      onChange={event => this.onDaysOfTheWeekUpdate('saturday',event.target.value)}
                    /> Saturday
                  </Label>
                </FormGroup>
                <FormGroup check>
                  <Label check>
                    <Input
                      type="checkbox"
                      value={daysOfTheWeeks.sunday}
                      disabled={!form.hasOwnProperty('end_date') ? true : false}
                      onChange={event => this.onDaysOfTheWeekUpdate('sunday',event.target.value)}
                    /> Sunday
                  </Label>
                </FormGroup>
                <br/>
                <Button color="success" onClick={this.submit}>Submit</Button> {' '}
                <Button color="danger" onClick={this.clearForm}>Cancel</Button>
              </Form>
            </Col>
            <Col md={8}>
              <div className='demo-app'>
                <div className='demo-app-calendar' style={{position:'relative'}}>
                  <button onClick={ this.previousMonth } style={{position:'absolute', left: 0}}>Prev</button>
                  <button onClick={ this.nextMonth } style={{position:'absolute', right: 0}}>Next</button>
                  <FullCalendar
                    defaultView="dayGridMonth"
                    header={{
                      left: '',
                      center: 'title',
                      right: ''
                    }}
                    plugins={[ dayGridPlugin ]}
                    ref={ this.calendarComponentRef }
                    events={calendarEvents}
                  />
                </div>
              </div>
            </Col>
          </Row>
        </Container>
      </div>
    );
  }
}

if (document.getElementById('example')) {
    ReactDOM.render(<Example />, document.getElementById('example'));
}
