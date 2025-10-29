/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package question1_app;

import java.beans.PropertyChangeListener;
import java.beans.PropertyChangeSupport;
import java.io.Serializable;
import java.util.Date;
import javax.persistence.Basic;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.Lob;
import javax.persistence.NamedQueries;
import javax.persistence.NamedQuery;
import javax.persistence.Table;
import javax.persistence.Temporal;
import javax.persistence.TemporalType;
import javax.persistence.Transient;

/**
 *
 * @author lastp
 */
@Entity
@Table(name = "patients_records", catalog = "patients_db", schema = "")
@NamedQueries({
    @NamedQuery(name = "PatientsRecords.findAll", query = "SELECT p FROM PatientsRecords p"),
    @NamedQuery(name = "PatientsRecords.findById", query = "SELECT p FROM PatientsRecords p WHERE p.id = :id"),
    @NamedQuery(name = "PatientsRecords.findByGender", query = "SELECT p FROM PatientsRecords p WHERE p.gender = :gender"),
    @NamedQuery(name = "PatientsRecords.findByFirstName", query = "SELECT p FROM PatientsRecords p WHERE p.firstName = :firstName"),
    @NamedQuery(name = "PatientsRecords.findByLastName", query = "SELECT p FROM PatientsRecords p WHERE p.lastName = :lastName"),
    @NamedQuery(name = "PatientsRecords.findByBirthDate", query = "SELECT p FROM PatientsRecords p WHERE p.birthDate = :birthDate"),
    @NamedQuery(name = "PatientsRecords.findByEmail", query = "SELECT p FROM PatientsRecords p WHERE p.email = :email")})
public class PatientsRecords implements Serializable {

    @Transient
    private PropertyChangeSupport changeSupport = new PropertyChangeSupport(this);

    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @Column(name = "id")
    private Integer id;
    @Basic(optional = false)
    @Column(name = "gender")
    private String gender;
    @Basic(optional = false)
    @Column(name = "first_name")
    private String firstName;
    @Basic(optional = false)
    @Column(name = "last_name")
    private String lastName;
    @Column(name = "birth_date")
    @Temporal(TemporalType.DATE)
    private Date birthDate;
    @Column(name = "email")
    private String email;
    @Lob
    @Column(name = "reason_for_visit")
    private String reasonForVisit;

    public PatientsRecords() {
    }

    public PatientsRecords(Integer id) {
        this.id = id;
    }

    public PatientsRecords(Integer id, String gender, String firstName, String lastName) {
        this.id = id;
        this.gender = gender;
        this.firstName = firstName;
        this.lastName = lastName;
    }

    public Integer getId() {
        return id;
    }

    public void setId(Integer id) {
        Integer oldId = this.id;
        this.id = id;
        changeSupport.firePropertyChange("id", oldId, id);
    }

    public String getGender() {
        return gender;
    }

    public void setGender(String gender) {
        String oldGender = this.gender;
        this.gender = gender;
        changeSupport.firePropertyChange("gender", oldGender, gender);
    }

    public String getFirstName() {
        return firstName;
    }

    public void setFirstName(String firstName) {
        String oldFirstName = this.firstName;
        this.firstName = firstName;
        changeSupport.firePropertyChange("firstName", oldFirstName, firstName);
    }

    public String getLastName() {
        return lastName;
    }

    public void setLastName(String lastName) {
        String oldLastName = this.lastName;
        this.lastName = lastName;
        changeSupport.firePropertyChange("lastName", oldLastName, lastName);
    }

    public Date getBirthDate() {
        return birthDate;
    }

    public void setBirthDate(Date birthDate) {
        Date oldBirthDate = this.birthDate;
        this.birthDate = birthDate;
        changeSupport.firePropertyChange("birthDate", oldBirthDate, birthDate);
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        String oldEmail = this.email;
        this.email = email;
        changeSupport.firePropertyChange("email", oldEmail, email);
    }

    public String getReasonForVisit() {
        return reasonForVisit;
    }

    public void setReasonForVisit(String reasonForVisit) {
        String oldReasonForVisit = this.reasonForVisit;
        this.reasonForVisit = reasonForVisit;
        changeSupport.firePropertyChange("reasonForVisit", oldReasonForVisit, reasonForVisit);
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (id != null ? id.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof PatientsRecords)) {
            return false;
        }
        PatientsRecords other = (PatientsRecords) object;
        if ((this.id == null && other.id != null) || (this.id != null && !this.id.equals(other.id))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "question1_app.PatientsRecords[ id=" + id + " ]";
    }

    public void addPropertyChangeListener(PropertyChangeListener listener) {
        changeSupport.addPropertyChangeListener(listener);
    }

    public void removePropertyChangeListener(PropertyChangeListener listener) {
        changeSupport.removePropertyChangeListener(listener);
    }
    
}
